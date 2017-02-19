<?php
/**
 * Created: 2017-02-19 14:57
 */

namespace VgaDatabase;

use \PDO;
use \VgaDatabase\Exceptions\VgaDatabaseConfigurationException;

class DatabaseConfig
{
    private $settings = [];

    public function __construct(string $pathToIniFile)
    {
        $this->setFromIniFile($pathToIniFile);
    }

    public function getDsn(): string
    {
        $driver = "{$this->settings['driver']}";
        $host = "host={$this->settings['host']}";
        if (!empty($this->settings['port'])) {
            $host .= ";port={$this->settings['port']}";
        }
        $dbname = "dbname={$this->settings['database']}";


        return "$driver:$host;$dbname;";
    }

    public function getUser(): string
    {
        return $this->settings['user'];
    }

    public function getPassword(): string
    {
        return $this->settings['password'];
    }

    public function getOptions(): array
    {
        $options = [];

        if (!empty($this->settings['persist']))
            $options[PDO::ATTR_PERSISTENT] = true;

        if (!empty($this->settings['errormode'])
            && $this->settings['errormode'] === 'silent'
        ) {
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
        } else {
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        }


        return $options;
    }

    private function setFromIniFile($pathToIniFile)
    {
        $this->settings = $this->parseIni($pathToIniFile);
    }

    /**
     * @param string $pathToIni path to the ini file containing the settings
     * @return array an array of parsed settings
     * @throws VgaDatabaseConfigurationException if required settings does not exsist
     */
    private function parseIni(string $pathToIni): array
    {
        $parsedIniArray = parse_ini_file($pathToIni);

        if (!empty($parsedIniArray['host'])
            || !empty($parsedIniArray['database'])
            || !empty($parsedIniArray['user'])
            || !empty($parsedIniArray['password'])
            || !empty($parsedIniArray['charset'])
        ) {
            return $parsedIniArray;
        } else {
            throw new VgaDatabaseConfigurationException($parsedIniArray);
        }
    }


}