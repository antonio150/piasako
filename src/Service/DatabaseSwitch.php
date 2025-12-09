<?php

namespace App\Service;

use App\Entity\Main\Site;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DatabaseSwitcher
{
    private EntityManagerInterface $entityManager;
    private Connection $connection;
    private ManagerRegistry $doctrine;
    private KernelInterface $kernel;
    

    public function __construct(
        EntityManagerInterface $entityManager, 
        ManagerRegistry $doctrine,
        private DynamicEntityManagerProvider $provider,
        KernelInterface $kernel
    ) {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
        $this->doctrine = $doctrine;
        $this->kernel = $kernel;
    }

    /**
     * Ici  nouvelles méthodes pour la creation d'utilisateur et la creation d'une nouvelle base de donne
     */
    /**
     * Méthode à APPELER via ton API pour tout faire dynamiquement
     */
    public function createDatabase(
        string $databaseName,
        array $siteData,
        $logoFileStock = null
    ): array|JsonResponse
    {

        $dbUser = $siteData["sitBddUser"];
        $dbPassword = $siteData["sitBddMdp"];
        // 1. Vérifs éventuelles (à adapter si utile)
        $errors = [];
        if ($this->entityManager->getRepository(Site::class)->findOneBy(['sitBddNom' => $databaseName])) {
            $errors[] = "nom de base de données";
        }
        if ($this->entityManager->getRepository(Site::class)->findOneBy(['sitRaisonsociale' => $siteData["sitRaisonsociale"] ?? null])) {
            $errors[] = "raison sociale";
        }
        if ($this->entityManager->getRepository(Site::class)->findOneBy(['sitMail' => $siteData["sitMail"] ?? null])) {
            $errors[] = "adresse mail";
        }
        if ($this->entityManager->getRepository(Site::class)->findOneBy(['sitCode' => $siteData["sitCode"] ?? null])) {
            $errors[] = "code de site";
        }
        if (!empty($errors)) {
            return new JsonResponse([
                'message' => "Un site avec ce " . implode(" ou ", $errors) . " existe déjà.",
                'status' => 400,
            ], 400);
        }

        // 2. CRÉATION BASE + USER + DROITS
        try {
            $this->createDatabaseAndUser($databaseName, $dbUser, $dbPassword);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'message' => 'Erreur création DB/User : ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }

        // 3. Création et mise à jour du schéma directement (sans modifier doctrine.yaml)
        try {
            $params = $this->connection->getParams();
            $host = $params['host'];
            $dbType = $_ENV['DB_TYPE'] ?? 'mysql'; // Lecture du type de base de données depuis .env

            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                // For PostgreSQL, grant privileges on the new database's public schema using main connection
                $mainParams = $this->connection->getParams();
                $newDbParams = $mainParams;
                $newDbParams['dbname'] = $databaseName;
                $tempConn = DriverManager::getConnection($newDbParams);
                $tempConn->executeStatement("GRANT ALL PRIVILEGES ON SCHEMA public TO \"$dbUser\"");
                $tempConn->executeStatement("ALTER SCHEMA public OWNER TO \"$dbUser\"");
                $tempConn->executeStatement("ALTER DEFAULT PRIVILEGES FOR ROLE \"$dbUser\" IN SCHEMA public GRANT ALL ON TABLES TO \"$dbUser\"");
                $tempConn->executeStatement("ALTER DEFAULT PRIVILEGES FOR ROLE \"$dbUser\" IN SCHEMA public GRANT ALL ON SEQUENCES TO \"$dbUser\"");
                $tempConn->executeStatement("ALTER DEFAULT PRIVILEGES FOR ROLE \"$dbUser\" IN SCHEMA public GRANT ALL ON FUNCTIONS TO \"$dbUser\"");
                $tempConn->executeStatement("GRANT CREATE ON SCHEMA public TO \"$dbUser\"");
                $tempConn->executeStatement("GRANT USAGE ON SCHEMA public TO \"$dbUser\"");
                $tempConn->executeStatement("ALTER ROLE \"$dbUser\" SET search_path TO public;");
                $tempConn->close();
            }

            // Définir le driver et les paramètres spécifiques au type de base de données
            $driver = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'pdo_pgsql' : 'pdo_mysql';
            $charset = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'utf8' : 'utf8mb4';

            // Port par défaut selon le type de base de données
            $defaultPort = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 5432 : 3306;
            $port = $params['port'] ?? $defaultPort;

            $connectionParams = [
                'dbname'   => $databaseName,
                'user'     => $dbUser,
                'password' => $dbPassword,
                'host'     => $host,
                'port'     => $port,
                'driver'   => $driver,
                'charset'  => $charset,
            ];

            // Configuration pour les entités Dynamic
            $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
                [$this->kernel->getProjectDir() . '/src/Entity/Dynamic'],
                true
            );

            // Connexion et EntityManager
            $connection = DriverManager::getConnection($connectionParams, $ormConfig);
            $entityManager = new EntityManager($connection, $ormConfig);

            // Mise à jour du schéma
            $this->updateSchemaFromMetadata($entityManager);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'message' => 'Erreur application du schéma : ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }

        // 5. Création (en mémoire ici, ou persist) de l’entité Site
        $site = new Site();
        $site->setSitRaisonsociale($siteData["sitRaisonsociale"] ?? null);
        $site->setSitAdresse($siteData["sitAdresse"] ?? null);
        $site->setSitTel($siteData["sitTel"] ?? null);
        $site->setSitMail($siteData["sitMail"] ?? null);
        $site->setSitCode($siteData["sitCode"] ?? null);
        $site->setSitBddNom($databaseName);
        $site->setSitBddUser($dbUser);
        $site->setSitBddMdp($dbPassword);
        $site->setestActif($siteData["sitInactif"] ?? false);
       
        /*$this->entityManager->persist($site);
        $this->entityManager->flush();*/

        // Retourner l'entité Site sans persister
        return [
            'site' => $site,
            'databaseName' => $databaseName,
        ];
    }

    // Les méthodes updateDoctrineConfig et runSchemaUpdate ont été supprimées car remplacées par updateSchemaFromMetadata

    /**
     * Crée BDD, USER et assigne les droits en utilisant le SGBD configuré
     */
    private function createDatabaseAndUser(string $databaseName, string $dbUser, string $dbPwd): void
    {
        $conn = $this->connection; // On suppose connecté avec le super-utilisateur/admin/root
        $dbType = $_ENV['DB_TYPE'] ?? 'mysql'; // Lecture du type de base de données depuis .env

            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                // PostgreSQL
                try {
                    // Crée la base de données
                    $databaseExists = $conn->executeQuery("SELECT 1 FROM pg_database WHERE datname = :databaseName", ['databaseName' => $databaseName])->fetchOne();
                    if ($databaseExists) {
                        // Terminate active connections and drop the database to ensure clean state
                        $conn->executeStatement("SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '$databaseName' AND pid <> pg_backend_pid()");
                        $conn->executeStatement("DROP DATABASE \"$databaseName\"");
                    }
                    $conn->executeStatement("CREATE DATABASE \"$databaseName\" TEMPLATE template0 ENCODING 'UTF8' LC_COLLATE 'en_US.UTF-8' LC_CTYPE 'en_US.UTF-8'");
                
                // Vérifie si l'utilisateur existe déjà
                $userExists = $conn->executeQuery("SELECT 1 FROM pg_roles WHERE rolname = :username", ['username' => $dbUser])->fetchOne();
                
                if (!$userExists) {
                    // Crée l'utilisateur
                    $conn->executeStatement(
                        "CREATE USER \"$dbUser\" WITH PASSWORD " . $conn->quote($dbPwd)
                    );
                }
                
                // Donne tous les droits sur la base
                 $conn->executeStatement("GRANT ALL PRIVILEGES ON DATABASE \"$databaseName\" TO \"$dbUser\"");
            } catch (\Exception $e) {
                // Gère les erreurs spécifiques à PostgreSQL
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    // Base ou utilisateur existe déjà, ce n'est pas une erreur critique
                } else {
                    throw $e; // Relance l'erreur pour les autres cas
                }
            }
        } else {
            // MySQL (comportement par défaut)
            try {
                // Drop if exists to ensure clean state
                $conn->executeStatement("DROP DATABASE IF EXISTS `$databaseName`");
                // Crée la base de données
                $conn->executeStatement("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Crée l'utilisateur si pas encore créé
                $conn->executeStatement(
                    "CREATE USER IF NOT EXISTS '{$dbUser}'@'%' IDENTIFIED BY :pwd",
                    ['pwd' => $dbPwd]
                );
                
                // Donne tous les droits sur la base
                $conn->executeStatement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$dbUser}'@'%'");
                
                // Applique les droits
                $conn->executeStatement("FLUSH PRIVILEGES");
            } catch (\Exception $e) {
                // Gère les erreurs spécifiques à MySQL
                throw $e;
            }
        }
    }

    public function dropDatabase(string $databaseName): void
    {
        $conn = $this->connection; // On suppose connecté avec le super-utilisateur/admin/root
        $dbType = $_ENV['DB_TYPE'] ?? 'mysql'; // Lecture du type de base de données depuis .env

        if ($dbType === 'postgresql' || $dbType === 'pgsql') {
            // PostgreSQL exige qu'aucune connexion active n'existe à la BDD avant suppression
            try {
                $conn->executeStatement("SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '$databaseName' AND pid <> pg_backend_pid()");
                $conn->executeStatement("DROP DATABASE IF EXISTS \"$databaseName\"");
            } catch (\Exception $e) {
                // Gère les erreurs spécifiques à PostgreSQL
                throw $e;
            }
        } else {
            // MySQL
            $conn->executeStatement("DROP DATABASE IF EXISTS `$databaseName`");
        }
    }

    public function switchDatabaseCreateSite(string $databaseName, $siteBddUser, $siteBddMdp): void
    {
        // 🔥 Nouvelle URL de connexion

        // Récupérer l'URL de la base de données actuelle
        $databaseUrl = $_ENV['DATABASE_URL_MAIN'] ?? getenv('DATABASE_URL_MAIN');
        $schemaManager = $this->connection->createSchemaManager();
        $existingDatabases = $schemaManager->listDatabases();
        $params = $this->connection->getParams();
        $dbType = $_ENV['DB_TYPE'] ?? 'mysql'; // Lecture du type de base de données depuis .env
        
        // Définir le driver et les paramètres spécifiques au type de base de données
        $driver = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'pdo_pgsql' : 'pdo_mysql';
        $charset = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'utf8' : 'utf8mb4';
        $serverVersion = ($dbType === 'postgresql' || $dbType === 'pgsql') ? '14' : '10.4.32-MariaDB';
        
        // Port par défaut selon le type de base de données
        $defaultPort = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 5432 : 3306;
        $port = $params['port'] ?? $defaultPort;

        try {
            $testConnection = DriverManager::getConnection([
                'driver' => $driver,
                'host' => $params['host'],
                'port' => $port,
                'dbname' => $databaseName,
                'user' => $siteBddUser,
                'password' => $siteBddMdp,
                'charset' => $charset,
            ]);
            // Exécuter une requête simple pour vérifier l'accès
            $testConnection->executeQuery('SELECT 1');
            $databaseExistsWithUser = true;
            $errors[] = "base de données avec cet utilisateur";
            $testConnection->close();
            
            // Construction de l'URL selon le type de base de données
            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                $newUrl = "postgresql://".rawurlencode($siteBddUser).":".rawurlencode($siteBddMdp)."@".$params['host'].":".$port."/".$databaseName."?serverVersion=".$serverVersion."&charset=".$charset;
            } else {
                $newUrl = "mysql://".rawurlencode($siteBddUser).":".rawurlencode($siteBddMdp)."@".$params['host'].":".$port."/".$databaseName."?serverVersion=".$serverVersion."&charset=".$charset;
            }

        } catch (\Exception $e) {
            // L'utilisateur n'a pas accès à la base, donc on peut continuer
            // Sélectionner la bonne URL en fonction du type de base de données
            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC_PGSQL'] ?? getenv('DATABASE_URL_DYNAMIC_PGSQL');
            } else {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC_MYSQL'] ?? getenv('DATABASE_URL_DYNAMIC_MYSQL');
            }
            // Fallback sur la variable générique si les spécifiques ne sont pas définies
            if (empty($newUrl)) {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC'] ?? getenv('DATABASE_URL_DYNAMIC');
            }
        }

        // ⚡ Modifier dynamiquement la connexion de `dynamic`
        $params = $this->entityManager->getConnection()->getParams();
        $params['url'] = $newUrl;

    

        // 🏗️ Créer un nouveau `EntityManager` pour la base dynamique
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../Entity/Dynamic'], // 📌 Chemin des entités
            true
        );
        $newConnection = DriverManager::getConnection($params);
        $newEntityManager = new EntityManager($newConnection, $config);

        // 🔄 Mettre à jour l'EntityManager courant
        $this->entityManager = $newEntityManager;
        // 🧠 Stocke dans le provider
        $this->provider->setEntityManager($newEntityManager);

    }

    public function switchDatabase(string $databaseName): void
    {
        // 🔥 Nouvelle URL de connexion

        // Récupérer l'URL de la base de données actuelle
        $databaseUrl = $_ENV['DATABASE_URL_MAIN'] ?? getenv('DATABASE_URL_MAIN');
        $schemaManager = $this->connection->createSchemaManager();
        $existingDatabases = $schemaManager->listDatabases();
        $params = $this->connection->getParams();
        $dbType = $_ENV['DB_TYPE'] ?? 'mysql'; // Lecture du type de base de données depuis .env

        // Récupérer l'EntityManager pour la base principale
        $mainEntityManager = $this->doctrine->getManager('default');

        $site = $mainEntityManager
        ->getRepository(\App\Entity\Main\Site::class)
        ->findOneBy(['sitBddNom' => $databaseName]);
        
        $siteBddUser = $site->getSitBddUser();
        $siteBddMdp = $site->getSitBddMdp();

        // Définir le driver et les paramètres spécifiques au type de base de données
        $driver = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'pdo_pgsql' : 'pdo_mysql';
        $charset = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 'utf8' : 'utf8mb4';
        $serverVersion = ($dbType === 'postgresql' || $dbType === 'pgsql') ? '14' : '10.4.32-MariaDB';
        
        // Port par défaut selon le type de base de données
        $defaultPort = ($dbType === 'postgresql' || $dbType === 'pgsql') ? 5432 : 3306;
        $port = $params['port'] ?? $defaultPort;

        try {
            $testConnection = DriverManager::getConnection([
                'driver' => $driver,
                'host' => $params['host'],
                'port' => $port,
                'dbname' => $databaseName,
                'user' => $siteBddUser,
                'password' => $siteBddMdp,
                'charset' => $charset,
            ]);
            // Exécuter une requête simple pour vérifier l'accès
            $testConnection->executeQuery('SELECT 1');
            $databaseExistsWithUser = true;
            $errors[] = "base de données avec cet utilisateur";
            $testConnection->close();
            
            // Construction de l'URL selon le type de base de données
            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                $newUrl = "postgresql://".rawurlencode($siteBddUser).":".rawurlencode($siteBddMdp)."@".$params['host'].":".$port."/".$databaseName."?serverVersion=".$serverVersion."&charset=".$charset;
            } else {
                $newUrl = "mysql://".rawurlencode($siteBddUser).":".rawurlencode($siteBddMdp)."@".$params['host'].":".$port."/".$databaseName."?serverVersion=".$serverVersion."&charset=".$charset;
            }
        } catch (\Exception $e) {
            // L'utilisateur n'a pas accès à la base, donc on peut continuer
            // Sélectionner la bonne URL en fonction du type de base de données
            if ($dbType === 'postgresql' || $dbType === 'pgsql') {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC_PGSQL'] ?? getenv('DATABASE_URL_DYNAMIC_PGSQL');
            } else {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC_MYSQL'] ?? getenv('DATABASE_URL_DYNAMIC_MYSQL');
            }
            // Fallback sur la variable générique si les spécifiques ne sont pas définies
            if (empty($newUrl)) {
                $newUrl = $_ENV['DATABASE_URL_DYNAMIC'] ?? getenv('DATABASE_URL_DYNAMIC');
            }
        }

        // ⚡ Modifier dynamiquement la connexion de `dynamic`
        $params = $this->entityManager->getConnection()->getParams();
        $params['url'] = $newUrl;

        // 🏗️ Créer un nouveau `EntityManager` pour la base dynamique
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../Entity/Dynamic'], // 📌 Chemin des entités
            true
        );
        $newConnection = DriverManager::getConnection($params);
        $newEntityManager = new EntityManager($newConnection, $config);
        
        // 🔄 Mettre à jour l'EntityManager courant
        $this->entityManager = $newEntityManager;
        // 🧠 Stocke dans le provider
        $this->provider->setEntityManager($newEntityManager);
    }

    /**
     * Met à jour le schéma de base de données en utilisant SchemaTool
     * comme dans UpdateTenantSchemasCommand
     */
    private function updateSchemaFromMetadata(EntityManager $entityManager): void
    {
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        if (count($metadata) > 0) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
            $schemaTool->updateSchema($metadata, true);
        }
    }
}
