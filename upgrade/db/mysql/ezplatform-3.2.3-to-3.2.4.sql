UPDATE ezuser SET email =
    CASE
        WHEN contentobject_id = 10 THEN 'anonymous@link.invalid'
        WHEN contentobject_id = 14 THEN 'admin@link.invalid'
    END
WHERE contentobject_id IN (10, 14) AND email = 'nospam@ez.no';
