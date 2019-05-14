<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190109095211 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel ADD shop_billing_data_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EB5282EDF FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_shop_billing_data (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119EB5282EDF ON sylius_channel (shop_billing_data_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EB5282EDF');
        $this->addSql('DROP TABLE sylius_shop_billing_data');
        $this->addSql('DROP INDEX UNIQ_16C8119EB5282EDF ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP shop_billing_data_id');
    }
}
