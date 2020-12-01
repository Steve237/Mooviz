<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201129184029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) DEFAULT NULL, CHANGE verifpayment verifpayment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD username_id INT NOT NULL, CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) DEFAULT NULL, CHANGE views views BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432ED766068 FOREIGN KEY (username_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_29AA6432ED766068 ON videos (username_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE verifpayment verifpayment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432ED766068');
        $this->addSql('DROP INDEX IDX_29AA6432ED766068 ON videos');
        $this->addSql('ALTER TABLE videos DROP username_id, CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE views views BIGINT DEFAULT NULL');
    }
}
