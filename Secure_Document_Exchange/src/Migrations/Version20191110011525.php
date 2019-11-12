<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191110011525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files ADD created_by_id INT NOT NULL, ADD updated_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6354059B03A8386 ON files (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6354059896DBBDE ON files (updated_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059B03A8386');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059896DBBDE');
        $this->addSql('DROP INDEX IDX_6354059B03A8386 ON files');
        $this->addSql('DROP INDEX IDX_6354059896DBBDE ON files');
        $this->addSql('ALTER TABLE files DROP created_by_id, DROP updated_by_id, DROP created_at, DROP updated_at');
    }
}
