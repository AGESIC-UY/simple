<?php

class Migration_12 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $sql = "CREATE FUNCTION `json_extract_c`(
            details TEXT,
            required_field VARCHAR (255)
            ) RETURNS text CHARSET latin1
            BEGIN
            SET details = SUBSTRING_INDEX(details,  '{', -1);
            SET details = SUBSTRING_INDEX(details, '}', 1);
            RETURN TRIM(
                BOTH '\"' FROM SUBSTRING_INDEX(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(
                            details,
                            CONCAT(
                                '\"',
                                SUBSTRING_INDEX(required_field,'$.', - 1),
                                '\":'
                            ),
                            - 1
                        ),
                        ',\"',
                        1
                    ),
                    ':',
                    -1
                )
            ) ;
            END";
        $q->execute('DROP FUNCTION IF EXISTS `json_extract_c`;');
        $q->execute($sql);
    }

    public function down() {
        
    }

}
