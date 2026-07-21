<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260721204353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feeding_schedule (id INT AUTO_INCREMENT NOT NULL, interval_days INT NOT NULL, species_id INT NOT NULL, life_stage_id INT NOT NULL, INDEX IDX_2775B149B2A1D860 (species_id), INDEX IDX_2775B1494B3860AC (life_stage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE feedings (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, quantity INT NOT NULL, spider_id INT NOT NULL, food_id INT NOT NULL, INDEX IDX_7974D9367D8B25C (spider_id), INDEX IDX_7974D936BA8E87C4 (food_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, food_species_id INT NOT NULL, food_size_id INT NOT NULL, INDEX IDX_D43829F7D5E468C1 (food_species_id), INDEX IDX_D43829F7612741F (food_size_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food_size (id INT AUTO_INCREMENT NOT NULL, size VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food_species (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE life_stage (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE molt (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, leg_span_cm DOUBLE PRECISION DEFAULT NULL, notes LONGTEXT DEFAULT NULL, spider_id INT NOT NULL, life_stage_id INT NOT NULL, INDEX IDX_35A3ED517D8B25C (spider_id), INDEX IDX_35A3ED514B3860AC (life_stage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE species (id INT AUTO_INCREMENT NOT NULL, latin_name VARCHAR(255) NOT NULL, common_name VARCHAR(255) DEFAULT NULL, habitat VARCHAR(20) NOT NULL, temp_min DOUBLE PRECISION NOT NULL, temp_max DOUBLE PRECISION NOT NULL, humidity_min DOUBLE PRECISION NOT NULL, humidity_max DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE spider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, picture_link VARCHAR(255) DEFAULT NULL, coloration VARCHAR(255) NOT NULL, date_aquired DATE NOT NULL, date_of_death DATE DEFAULT NULL, archived_at DATE DEFAULT NULL, species_id INT NOT NULL, initial_life_stage_id INT NOT NULL, INDEX IDX_6AD76A84B2A1D860 (species_id), INDEX IDX_6AD76A846E94A218 (initial_life_stage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE feeding_schedule ADD CONSTRAINT FK_2775B149B2A1D860 FOREIGN KEY (species_id) REFERENCES species (id)');
        $this->addSql('ALTER TABLE feeding_schedule ADD CONSTRAINT FK_2775B1494B3860AC FOREIGN KEY (life_stage_id) REFERENCES life_stage (id)');
        $this->addSql('ALTER TABLE feedings ADD CONSTRAINT FK_7974D9367D8B25C FOREIGN KEY (spider_id) REFERENCES spider (id)');
        $this->addSql('ALTER TABLE feedings ADD CONSTRAINT FK_7974D936BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7D5E468C1 FOREIGN KEY (food_species_id) REFERENCES food_species (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7612741F FOREIGN KEY (food_size_id) REFERENCES food_size (id)');
        $this->addSql('ALTER TABLE molt ADD CONSTRAINT FK_35A3ED517D8B25C FOREIGN KEY (spider_id) REFERENCES spider (id)');
        $this->addSql('ALTER TABLE molt ADD CONSTRAINT FK_35A3ED514B3860AC FOREIGN KEY (life_stage_id) REFERENCES life_stage (id)');
        $this->addSql('ALTER TABLE spider ADD CONSTRAINT FK_6AD76A84B2A1D860 FOREIGN KEY (species_id) REFERENCES species (id)');
        $this->addSql('ALTER TABLE spider ADD CONSTRAINT FK_6AD76A846E94A218 FOREIGN KEY (initial_life_stage_id) REFERENCES life_stage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feeding_schedule DROP FOREIGN KEY FK_2775B149B2A1D860');
        $this->addSql('ALTER TABLE feeding_schedule DROP FOREIGN KEY FK_2775B1494B3860AC');
        $this->addSql('ALTER TABLE feedings DROP FOREIGN KEY FK_7974D9367D8B25C');
        $this->addSql('ALTER TABLE feedings DROP FOREIGN KEY FK_7974D936BA8E87C4');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7D5E468C1');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7612741F');
        $this->addSql('ALTER TABLE molt DROP FOREIGN KEY FK_35A3ED517D8B25C');
        $this->addSql('ALTER TABLE molt DROP FOREIGN KEY FK_35A3ED514B3860AC');
        $this->addSql('ALTER TABLE spider DROP FOREIGN KEY FK_6AD76A84B2A1D860');
        $this->addSql('ALTER TABLE spider DROP FOREIGN KEY FK_6AD76A846E94A218');
        $this->addSql('DROP TABLE feeding_schedule');
        $this->addSql('DROP TABLE feedings');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_size');
        $this->addSql('DROP TABLE food_species');
        $this->addSql('DROP TABLE life_stage');
        $this->addSql('DROP TABLE molt');
        $this->addSql('DROP TABLE species');
        $this->addSql('DROP TABLE spider');
    }
}
