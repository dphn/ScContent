<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Db;

use Zend\Validator\AbstractValidator,
    //
    Exception,
    PDO;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Connection extends AbstractValidator
{
    /**#@+
     * @const string
     */
    const MissingOptions         = 'Missing Options';
    const MissingSQLitePath      = 'Missing SQLite path';
    const SQLiteConnectionFailed = 'SQLite Connection failed';
    const ConnectionFailed       = 'Connection Failed';
    /**#@-*/

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::MissingOptions
            => "Missing options. The database connection failed.",

        self::MissingSQLitePath
            => "Missing option 'path' for SQLite.",

        self::SQLiteConnectionFailed
            => "Connection to the SQLite failed. Please, check the SQLite path.",

        self::ConnectionFailed
            => "The database connection failed. Please, check the entered options.",
    ];

    /**
     * @param  string $value
     * @param  null|array $c context
     * @return boolean
     */
    public function isValid($value, $c = null)
    {
        if(!is_array($c) || empty($c)) {
            $this->error(self::MissingOptions);
            return false;
        }
        if($c['driver'] === 'sqlite') {
            return $this->validateSQLite($c);
        }
        if ((! isset($c['database'])     || empty($c['database']))
             || (! isset($c['host'])     || empty($c['host']))
             || (! isset($c['username']) || empty($c['username']))
             ||  ! isset($c['password'])
        ) {
            $this->error(self::MissingOptions);
            return false;
        }
        try {
            $dsn = $c['driver'] . ':host=' . $c['host'] . ';database=' . $c['database'];
            @$dbh = new PDO($dsn, $c['username'], $c['password']);
        } catch (Exception $e) {
            $this->error(self::ConnectionFailed);
            return false;
        }
        return true;
    }

    /**
     * @param  array $c Context
     * @return boolean
     */
    protected function validateSQLite($c)
    {
        if (! isset($c['path']) || empty($c['path'])) {
            $this->error(self::MissingSQLitePath);
            return false;
        }
        if (! realpath($c['path'])) {
            $this->error(self::SQLiteConnectionFailed);
            return false;
        }
        return true;
    }
}
