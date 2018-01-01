<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180101125228 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pokemon ADD feed_id INT DEFAULT NULL, CHANGE pokedex_number pokedex_number INT DEFAULT NULL, CHANGE iv iv NUMERIC(4, 1) DEFAULT NULL, CHANGE cp cp INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F351A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id)');
        $this->addSql('CREATE INDEX IDX_62DC90F351A5BC03 ON pokemon (feed_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pokemon DROP FOREIGN KEY FK_62DC90F351A5BC03');
        $this->addSql('DROP INDEX IDX_62DC90F351A5BC03 ON pokemon');
        $this->addSql('ALTER TABLE pokemon DROP feed_id, CHANGE pokedex_number pokedex_number INT NOT NULL, CHANGE iv iv NUMERIC(4, 1) NOT NULL, CHANGE cp cp INT NOT NULL');
    }
}
