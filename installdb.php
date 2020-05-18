<?php
    require_once 'obj/Connection.php';
    echo "### Mini auto database setup <br><br>";
    $conn = null;
    try {
        $conn = new Connection();
        $c = $conn->conn;
    }
    catch(PDOException $e) {
        echo "Couldn't connect to the database. Make sure the credentials are correct and the database exists";
        exit;
    }

    if(is_null($c)) {
        echo "Couldn't connect to the database.";
        exit;
    }

    echo "Creating table 'discs'...<br>";

    try {
        $c->exec("CREATE TABLE `discs` (
            `id` int NOT NULL,
            `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `space` bigint NOT NULL DEFAULT '10737418240',
            `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `temporary` tinyint(1) NOT NULL DEFAULT '0',
            `permission_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'discs' table.";
        exit;
    }

    echo "Creating table 'discs_users'...<br>";

    try {
        $c->exec("CREATE TABLE `discs_users` (
            `id` int NOT NULL,
            `user_id` int NOT NULL,
            `disc_id` int NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'discs_users' table.";
        exit;
    }

    echo "Creating table 'files'...<br>";

    try {
        $c->exec("CREATE TABLE `files` (
            `id` bigint UNSIGNED NOT NULL,
            `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `key_name` varchar(256) NOT NULL,
            `isDir` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether this is a directory or not',
            `parent_id` int NOT NULL DEFAULT '0' COMMENT 'The id of the parent directory',
            `size` bigint NOT NULL DEFAULT '0',
            `binary_data` longblob
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'files' table.";
        exit;
    }

    echo "Creating table 'files_discs'...<br>";

    try {
        $c->exec("CREATE TABLE `files_discs` (
            `id` bigint UNSIGNED NOT NULL,
            `disc_id` int NOT NULL,
            `file_id` bigint UNSIGNED NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'files_discs' table.";
        exit;
    }

    echo "Creating table 'settings'...<br>";

    try {
        $c->exec("CREATE TABLE `settings` (
            `id` int NOT NULL,
            `user_id` int NOT NULL,
            `show_context_menu` tinyint(1) NOT NULL DEFAULT '1',
            `show_file_extension` tinyint(1) NOT NULL DEFAULT '1'
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'settings' table.";
        exit;
    }

    echo "Creating table 'users'...<br>";

    try {
        $c->exec("CREATE TABLE `users` (
            `id` int NOT NULL,
            `email` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'users' table.";
        exit;
    }
    
    echo "Setting up tables...<br>";

    try {
        $c->exec("ALTER TABLE `discs`
        ADD PRIMARY KEY (`id`),
        ADD UNIQUE KEY `unique_permid` (`permission_id`);");
         

        $c->exec( "ALTER TABLE `discs_users`
        ADD PRIMARY KEY (`id`),
        ADD KEY `fk_users_id` (`user_id`),
        ADD KEY `fk_discu_id` (`disc_id`) USING BTREE;");
         

        $c->exec(  "ALTER TABLE `files`
        ADD PRIMARY KEY (`id`),
        ADD UNIQUE KEY `key_name_unique` (`key_name`);");
         

        $c->exec(  "ALTER TABLE `files_discs`
        ADD PRIMARY KEY (`id`),
        ADD KEY `fk_discs_id` (`disc_id`),
        ADD KEY `fk_files_id` (`file_id`);");
         

        $c->exec(  "ALTER TABLE `settings`
        ADD PRIMARY KEY (`id`),
        ADD UNIQUE KEY `SETTINGS_USER_ID_UNIQUE` (`user_id`);");
         

        $c->exec(  "ALTER TABLE `users`
        ADD PRIMARY KEY (`id`),
        ADD UNIQUE KEY `email_unique` (`email`);");
         

        $c->exec(  "ALTER TABLE `discs`
        MODIFY `id` int NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  "ALTER TABLE `discs_users`
        MODIFY `id` int NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  "ALTER TABLE `files`
        MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  
            "ALTER TABLE `files_discs`
              MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  "ALTER TABLE `settings`
        MODIFY `id` int NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  "ALTER TABLE `users`
        MODIFY `id` int NOT NULL AUTO_INCREMENT;");
         

        $c->exec(  "ALTER TABLE `discs_users`
        ADD CONSTRAINT `fk_dusers_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `fk_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"
      );
         

        $c->exec(  "ALTER TABLE `files_discs`
        ADD CONSTRAINT `fk_discs_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `fk_files_id` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"
      );
         

        $c->exec(   "ALTER TABLE `settings`
        ADD CONSTRAINT `SETTINGS_USER_ID_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
         
    }
    catch(PDOException $e) {
        echo "Error: Couldn't create 'users' table.";
        exit;
    }
  

?>