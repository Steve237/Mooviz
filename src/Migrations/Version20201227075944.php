<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201227075944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, origin_id INT NOT NULL, destination_id INT DEFAULT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_6000B0D356A273CC (origin_id), INDEX IDX_6000B0D3816C6140 (destination_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D356A273CC FOREIGN KEY (origin_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3816C6140 FOREIGN KEY (destination_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE username_id username_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A29C1004E FOREIGN KEY (video_id) REFERENCES videos (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A29C1004E ON comments (video_id)');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) DEFAULT NULL, CHANGE verifpayment verifpayment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) DEFAULT NULL, CHANGE views views BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE notifications');
        $this->addSql('ALTER TABLE avatar CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A29C1004E');
        $this->addSql('DROP INDEX IDX_5F9E962A29C1004E ON comments');
        $this->addSql('ALTER TABLE comments CHANGE username_id username_id INT NOT NULL');
        $this->addSql('ALTER TABLE playlist CHANGE user_id user_id INT DEFAULT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE payment_status payment_status VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE verifpayment verifpayment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE subscription_id subscription_id INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE video_like CHANGE video_id video_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos CHANGE category_id category_id INT DEFAULT NULL, CHANGE sliderimage sliderimage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE views views BIGINT DEFAULT NULL');
    }
}
