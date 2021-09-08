<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Database;
use App\Repository\BackupRepository;
use App\Repository\DatabaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

class BackupService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private BackupRepository $backupRepository,
        private DatabaseRepository $databaseRepository,
        private string $projectDir,
        private Encryptor $encryptor,
        private DownloadHandler $downloadHandler,
        private MailerInterface $mailer,
        private TranslatorInterface $translator,
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
                ->setDatabase($database);

            $this->manager->persist($backup);
            $this->manager->flush();

            // Delete temp file from local project
            $fileSystem = new Filesystem();
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

    public function clean(Database $database): void
    {
        $maxBackups = $database->getMaxBackups();
        $backups = $database->getBackups();

        if (\count($backups) > $maxBackups) {
            for ($i = $maxBackups, $iMax = \count($backups); $i < $iMax; ++$i) {
                $deleteBackup = $backups[$i];
                $this->manager->remove($deleteBackup);
            }
        }

        $this->manager->flush();
    }

    /**
     * @throws \Exception
     */
    public function downloadBackupFile(Backup $backup): StreamedResponse
    {
        return $this->downloadHandler->downloadObject($backup, 'backupFile');
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
            [
                'add-drop-table' => true,
            ]
        );
    }
}
