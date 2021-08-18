<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Database;
use App\Repository\BackupRepository;
use App\Repository\DatabaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Handler\DownloadHandler;

class BackupService
{
    public const MAX_ROWS = 30;

    /**
     * @throws \Exception
     */
    public function __construct(
        private EntityManagerInterface $manager,
        private BackupRepository $backupRepository,
        private DatabaseRepository $databaseRepository,
        private string $projectDir,
        private Encryptor $encryptor,
        private DownloadHandler $downloadHandler,
    ){}

    public function backup(Database $database, string $context)
    {
        // Define mysqldump object
        $mysqldump = $this->defineMysqlDumpObject($database);

        // Define temp path
        $filepath = sprintf(
            '%s/backup_%s_hash_%s.sql',
            $this->projectDir,
            (new \DateTime())->format('d_m_y'),
            random_int(1000,99999999),
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
            ->setDb($database);

        $this->manager->persist($backup);
        $this->manager->flush();

        // Delete temp file from local project
        $fileSystem = new Filesystem();
        $fileSystem->remove($filepath);
    }

    public function clean(Database $database)
    {
        $maxBackups = $database->getMaxBackups();
        $backups = $database->getBackups();

        if(count($backups) > $maxBackups)
        {
            for($i=$maxBackups, $iMax = count($backups); $i < $iMax; $i++)
            {
                $deleteBackup = $backups[$i];
                $this->manager->remove($deleteBackup);
            }
        }

        $this->manager->flush();
    }

    /**
     * @throws \Exception
     */
    private function defineMysqlDumpObject(Database $database): Mysqldump
    {
        if($database->getPort())
        {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s',
                $database->getHost(),
                $database->getDbName()
            );
        } else {
            $dsn = sprintf(
                'mysql:host=%s:%s;dbname=%s',
                $database->getHost(),
                $database->getPort(),
                $database->getDbName()
            );
        }

        return new Mysqldump(
            $dsn,
            $database->getDbUser(),
            $this->encryptor->decrypt($database->getDbPassword()),
            [
                'add-drop-table' => true
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function downloadBackupFile(Backup $backup)
    {
        return $this->downloadHandler->downloadObject($backup, 'backupFile');
    }

    /**
     * @return Backup[]
     */
    public function getBackups()
    {
        return $this->backupRepository->findAll();
    }

    public function getDatabases()
    {
        return $this->databaseRepository->findAll();
    }
}