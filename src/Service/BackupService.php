<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Database;
use App\Helper\FlysystemHelper;
use App\Repository\BackupRepository;
use App\Repository\DatabaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BackupService
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly BackupRepository $backupRepository,
        private readonly DatabaseRepository $databaseRepository,
        private readonly string $projectDir,
        private readonly Encryptor $encryptor,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly FlysystemHelper $flysystemHelper,
    ) {
    }

    public function backup(Database $database, string $context): BackupStatus
    {
        try {
            // Define mysqldump object
            $mysqldump = $this->defineMysqlDumpObject($database);

            // Define temp path
            $filepath = sprintf(
                '%s/backup_%s_hash_%s.sql',
                $this->projectDir,
                (new \DateTime())->format('d_m_y'),
                random_int(1000, 99999999),
            );

            // Launch backup
            $mysqldump->start($filepath);

            // Get file infos
            $fileInfo = pathinfo($filepath);

            // Generate Uploaded file
            $uploadedFile = new UploadedFile(
                $filepath,
                $fileInfo['basename'],
                sprintf('application/%s', $fileInfo['extension']),
                null,
                true
            );

            // Create backup entity row and applied uploaded file
            $backup = new Backup();

            $backup->setContext($context)
                ->setBackupFile($uploadedFile)
                ->setDatabase($database)
                ->setBackupFileName($fileInfo['basename'])
                ->setBackupFileSize($uploadedFile->getSize())
                ->setMimeType($uploadedFile->getMimeType());

            $this->manager->persist($backup);
            $this->manager->flush();

            // Delete temp file from local project
            $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
            $fileSystem->remove($filepath);

            $backupStatus = new BackupStatus(BackupStatus::STATUS_OK);
        } catch (\Exception $e) {
            $backupStatus = new BackupStatus(BackupStatus::STATUS_FAIL, $e->getMessage());
        }

        if ($database->getOwner()->getReceiveAutomaticEmails()) {
            $locale = $database->getOwner()->getLocale();
            $subject = $this->translator->trans('backup_done.subject.' . $backupStatus->getStatus(), [
                '%dsn%' => $database->getDisplayDsn(),
            ], 'email', $locale);
            $content = $this->translator->trans('backup_done.content.' . $backupStatus->getStatus(), [
                '%dsn%' => $database->getDisplayDsn(),
                '%error%' => $backupStatus->getErrorMessage(),
            ], 'email', $locale);

            $email = (new NotificationEmail())
                ->subject($subject)
                ->content($content)
                ->to($database->getOwner()->getEmail())
                ->markAsPublic()
                ->context(['footer_text' => $this->translator->trans('backup_done.footer', [], 'email', $locale)]);
            $this->mailer->send($email);
        }

        return $backupStatus;
    }

    public function import(Backup $backup): void
    {
        $content = $this->flysystemHelper->getContent($backup);
        $database = $backup->getDatabase();

        $mysqlHost = $database->getHost();
        $mysqlUser = $database->getUser();
        $mysqlPassword = $this->encryptor->decrypt($database->getPassword());
        $mysqlDatabase = $database->getName();
        $mysqlPort = $database->getPort();

        $config = new Configuration();
        $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
        $conn = DriverManager::getConnection([
            'dbname' => $mysqlDatabase,
            'user' => $mysqlUser,
            'password' => $mysqlPassword,
            'host' => $mysqlHost,
            'driver' => 'mysqli',
            'port' => $mysqlPort,
        ], $config, new EventManager());

        $query = '';
        $temp = tmpfile();
        fwrite($temp, $content);
        $sqlScript = file(stream_get_meta_data($temp)['uri']);

        foreach ($sqlScript as $line) {
            $startWith = substr(trim($line), 0, 2);
            $endWith = substr(trim($line), -1, 1);

            if (empty($line) || '--' === $startWith || '//' === $startWith) {
                continue;
            }

            $query = $query . $line;
            if (';' === $endWith) {
                $conn->executeStatement($query);
                $query = '';
            }
        }
    }

    public function clean(Database $database): void
    {
        $backupCollection = new ArrayCollection(
            $this->backupRepository->getActiveBackups($database)
        );

        $criteria = new Criteria();
        $comparison = new Comparison(
            'id',
            Comparison::NIN,
            $backupCollection->map(
                function (Backup $backup) {
                    return $backup->getId();
                }
            )->getValues()
        );
        $criteria->where($comparison);

        /** @var Collection<int,Backup> $backupToBeDeletedCollection */
        $backupToBeDeletedCollection = $database->getBackups()->matching($criteria);

        foreach ($backupToBeDeletedCollection as $backup) {
            $this->flysystemHelper->remove($backup);
            $this->backupRepository->remove($backup);
        }

        $this->backupRepository->flush();
    }

    /**
     * @return Backup[]
     */
    public function getBackups(): array
    {
        return $this->backupRepository->findAll();
    }

    /**
     * @return Database[]
     */
    public function getDatabases(): array
    {
        return $this->databaseRepository->findAll();
    }

    /**
     * @throws \Exception
     */
    private function defineMysqlDumpObject(Database $database): Mysqldump
    {
        return new Mysqldump(
            $database->getDsn(),
            $database->getUser(),
            $this->encryptor->decrypt($database->getPassword()),
            $database->getBackupOptions(),
        );
    }
}
