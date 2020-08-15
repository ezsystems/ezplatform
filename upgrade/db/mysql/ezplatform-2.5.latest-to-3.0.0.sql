-- Product name migration
START TRANSACTION;
DELETE FROM ezsite_data WHERE name IN ('ezpublish-version', 'ezplatform-release');
INSERT INTO ezsite_data (name, value) VALUES ('ezplatform-release', '3.0.0');
COMMIT;

--
ALTER TABLE ezcontentclass_attribute MODIFY data_text1 VARCHAR(255);
--

--
ALTER TABLE ezcontentclass_attribute ADD COLUMN is_thumbnail TINYINT(1) NOT NULL DEFAULT '0';
--

-- EZP-31471: Keywords versioning
ALTER TABLE `ezkeyword_attribute_link`
    ADD COLUMN `version` INT(11) NOT NULL,
    ADD KEY `ezkeyword_attr_link_oaid_ver` (`objectattribute_id`, `version`);

UPDATE `ezkeyword_attribute_link` SET `version` = (
    SELECT `current_version`
    FROM `ezcontentobject_attribute` AS `cattr`
    JOIN `ezcontentobject` AS `contentobj`
        ON `cattr`.`contentobject_id` = `contentobj`.`id`
        AND `cattr`.`version` = `contentobj`.`current_version`
    WHERE `cattr`.`id` = `ezkeyword_attribute_link`.`objectattribute_id`
);
--

-- EZP-31079: Provided default value for ezuser login pattern --
UPDATE `ezcontentclass_attribute` SET `data_text2` = '^[^@]+$'
    WHERE `data_type_string` = 'ezuser'
    AND `data_text2` IS NULL;
--
