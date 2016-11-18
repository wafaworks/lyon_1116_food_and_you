<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160312121547 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, code VARCHAR(255) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, merchant_id VARCHAR(255) DEFAULT NULL, merchant_country VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, payment_means VARCHAR(255) DEFAULT NULL, transmission_date DATETIME DEFAULT NULL, payment_date DATETIME DEFAULT NULL, response_code VARCHAR(255) DEFAULT NULL, payment_certificate VARCHAR(255) DEFAULT NULL, authorisation_id VARCHAR(255) DEFAULT NULL, currency_code SMALLINT DEFAULT NULL, card_number VARCHAR(255) DEFAULT NULL, cvv_flag VARCHAR(255) DEFAULT NULL, cvv_response_code VARCHAR(255) DEFAULT NULL, bank_response_code VARCHAR(255) DEFAULT NULL, complementary_code VARCHAR(255) DEFAULT NULL, complementary_info VARCHAR(255) DEFAULT NULL, return_context VARCHAR(255) DEFAULT NULL, caddie VARCHAR(255) DEFAULT NULL, receipt_complement VARCHAR(255) DEFAULT NULL, merchant_language VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, customer_id VARCHAR(255) DEFAULT NULL, order_id VARCHAR(255) DEFAULT NULL, customer_email VARCHAR(255) DEFAULT NULL, customer_ip_address VARCHAR(255) DEFAULT NULL, capture_day VARCHAR(255) DEFAULT NULL, capture_mode VARCHAR(255) DEFAULT NULL, data VARCHAR(255) DEFAULT NULL, order_validity VARCHAR(255) DEFAULT NULL, transaction_condition VARCHAR(255) DEFAULT NULL, statement_reference VARCHAR(255) DEFAULT NULL, card_validity VARCHAR(255) DEFAULT NULL, score_value VARCHAR(255) DEFAULT NULL, score_color VARCHAR(255) DEFAULT NULL, score_info VARCHAR(255) DEFAULT NULL, score_threshold VARCHAR(255) DEFAULT NULL, score_profile VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD confirmed_reservations SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD transaction_id INT NOT NULL, ADD places SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849552FC0CB0F ON reservation (transaction_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552FC0CB0F');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('ALTER TABLE event DROP confirmed_reservations');
        $this->addSql('DROP INDEX UNIQ_42C849552FC0CB0F ON reservation');
        $this->addSql('ALTER TABLE reservation DROP transaction_id, DROP places');
    }
}
