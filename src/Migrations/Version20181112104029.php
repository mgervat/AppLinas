<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181112104029 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rating_article DROP FOREIGN KEY FK_E176F71EA32EFC6');
        $this->addSql('ALTER TABLE rating_user DROP FOREIGN KEY FK_49EB45CCA32EFC6');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE rating_article');
        $this->addSql('DROP TABLE rating_user');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A32EFC6');
        $this->addSql('DROP INDEX IDX_23A0E66A32EFC6 ON article');
        $this->addSql('ALTER TABLE article DROP rating_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating_article (rating_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_E176F71EA32EFC6 (rating_id), INDEX IDX_E176F71E7294869C (article_id), PRIMARY KEY(rating_id, article_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating_user (rating_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_49EB45CCA32EFC6 (rating_id), INDEX IDX_49EB45CCA76ED395 (user_id), PRIMARY KEY(rating_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rating_article ADD CONSTRAINT FK_E176F71E7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating_article ADD CONSTRAINT FK_E176F71EA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating_user ADD CONSTRAINT FK_49EB45CCA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating_user ADD CONSTRAINT FK_49EB45CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD rating_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A32EFC6 FOREIGN KEY (rating_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A32EFC6 ON article (rating_id)');
    }
}
