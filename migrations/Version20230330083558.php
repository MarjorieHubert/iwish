<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330083558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishes DROP FOREIGN KEY FK_DD0FA3689D86650F');
        $this->addSql('DROP INDEX IDX_DD0FA3689D86650F ON wishes');
        $this->addSql('ALTER TABLE wishes CHANGE user_id_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wishes ADD CONSTRAINT FK_DD0FA368A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD0FA368A76ED395 ON wishes (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishes DROP FOREIGN KEY FK_DD0FA368A76ED395');
        $this->addSql('DROP INDEX IDX_DD0FA368A76ED395 ON wishes');
        $this->addSql('ALTER TABLE wishes CHANGE user_id user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wishes ADD CONSTRAINT FK_DD0FA3689D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD0FA3689D86650F ON wishes (user_id_id)');
    }
}
