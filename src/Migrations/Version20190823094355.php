<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190823094355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE incoming_webhook (api_key VARCHAR(16) NOT NULL, actor_id VARCHAR(48) DEFAULT NULL, UNIQUE INDEX UNIQ_8FA504B410DAF24A (actor_id), PRIMARY KEY(api_key)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_92FB68EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT NOT NULL, actor_id VARCHAR(48) DEFAULT NULL, uuid VARCHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64910DAF24A (actor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE object (id VARCHAR(48) NOT NULL, location_id VARCHAR(48) DEFAULT NULL, attachment_id VARCHAR(48) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, summary LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, published DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, class_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A8ADABEC64D218E (location_id), UNIQUE INDEX UNIQ_A8ADABEC464E68B (attachment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (tagged_object_id VARCHAR(48) NOT NULL, tag_id VARCHAR(48) NOT NULL, INDEX IDX_389B7835B7EB2CD (tagged_object_id), INDEX IDX_389B783BAD26311 (tag_id), PRIMARY KEY(tagged_object_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity (id VARCHAR(48) NOT NULL, actor_id VARCHAR(48) DEFAULT NULL, object_id VARCHAR(48) DEFAULT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_AC74095A10DAF24A (actor_id), INDEX IDX_AC74095A232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_receiving_actor (activity_id VARCHAR(48) NOT NULL, actor_id VARCHAR(48) NOT NULL, INDEX IDX_EB60FC2381C06096 (activity_id), INDEX IDX_EB60FC2310DAF24A (actor_id), PRIMARY KEY(activity_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id VARCHAR(48) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor (id VARCHAR(48) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_447556F9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE following (follower VARCHAR(48) NOT NULL, following VARCHAR(48) NOT NULL, INDEX IDX_71BF8DE3B9D60946 (follower), INDEX IDX_71BF8DE371BF8DE3 (following), PRIMARY KEY(follower, following)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE authorization (controlled_actor_id VARCHAR(48) NOT NULL, controlling_actor_id VARCHAR(48) NOT NULL, INDEX IDX_7A6D8BEFD6EEC6FE (controlled_actor_id), INDEX IDX_7A6D8BEF6E512342 (controlling_actor_id), PRIMARY KEY(controlled_actor_id, controlling_actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE incoming_webhook ADD CONSTRAINT FK_8FA504B410DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64910DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC64D218E FOREIGN KEY (location_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC464E68B FOREIGN KEY (attachment_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7835B7EB2CD FOREIGN KEY (tagged_object_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783BAD26311 FOREIGN KEY (tag_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A232D562B FOREIGN KEY (object_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ABF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC2381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC2310DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDBF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor ADD CONSTRAINT FK_447556F9BF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3B9D60946 FOREIGN KEY (follower) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE371BF8DE3 FOREIGN KEY (following) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEFD6EEC6FE FOREIGN KEY (controlled_actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEF6E512342 FOREIGN KEY (controlling_actor_id) REFERENCES actor (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EA76ED395');
        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC464E68B');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7835B7EB2CD');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783BAD26311');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A232D562B');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095ABF396750');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDBF396750');
        $this->addSql('ALTER TABLE actor DROP FOREIGN KEY FK_447556F9BF396750');
        $this->addSql('ALTER TABLE activity_receiving_actor DROP FOREIGN KEY FK_EB60FC2381C06096');
        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC64D218E');
        $this->addSql('ALTER TABLE incoming_webhook DROP FOREIGN KEY FK_8FA504B410DAF24A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64910DAF24A');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A10DAF24A');
        $this->addSql('ALTER TABLE activity_receiving_actor DROP FOREIGN KEY FK_EB60FC2310DAF24A');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3B9D60946');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE371BF8DE3');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEFD6EEC6FE');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEF6E512342');
        $this->addSql('DROP TABLE incoming_webhook');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE object');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_receiving_actor');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE following');
        $this->addSql('DROP TABLE authorization');
    }
}
