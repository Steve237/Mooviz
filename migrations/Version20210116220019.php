<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210116220019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE username_id username_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications CHANGE destination_id destination_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) DEFAULT NULL, CHANGE verifpayment verifpayment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD customerid VARCHAR(255) DEFAULT NULL, CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) DEFAULT NULL, CHANGE views views BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, executed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE username_id username_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications CHANGE destination_id destination_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE verifpayment verifpayment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users DROP customerid, CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE views views BIGINT DEFAULT NULL');
    }
}
