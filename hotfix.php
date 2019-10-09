<?php

function red($text) {
	return "\e[31m".$text."\033[0m";
}
function yellow($text) {
	return "\e[33m".$text."\033[0m";
}
function blue($text) {
	return "\e[36m".$text."\033[0m";
}
function green($text) {
	return "\e[32m".$text."\033[0m";
}

function printLn($text) {
    return $text . "\n";
}

function getNextTag() {
    $out = array_filter(explode("\n", shell_exec('git tag')));
	list($major, $minor, $patch) = explode(".", end($out));
	return sprintf("%s.%s.%s", $major, $minor, $patch+1);
}

function getCurrentBranch() {
    $out = array_filter(explode("\n", shell_exec('git rev-parse --abbrev-ref HEAD')));
    return end($out);
}

function getHotfixBranch() {
    $out = array_filter(explode("\n", shell_exec('git branch --list hotfix/*')));
    return end($out);
}

function hotfixCheckReqs(Config $conf) {
    $out = array_filter(explode("\n", shell_exec('git diff-index --quiet HEAD --')));
    if (end($out) || !$conf->hotfixBranch) {
        echo printLn(red('Нет изменений'));
        exit;
    }

    $out = array_filter(explode("\n", shell_exec('git flow config &> /dev/null')));

    if (!end($out)) {
        echo printLn(red('Не git flow репозиторий. Сначала выполните `git flow init`'));
        exit;
    }
}

class Config
{
    public $message = null;
    public $verbose = false;
    public $push = false;
    public $stash = false;
    public $rebase = false;
    public $hotfixBranch = null;

    public function __construct($argv)
    {
        $this->parseArgs($argv);
    }

    protected function parseArgs($args) {
        foreach ($args as $arg) {
            if ($arg[0] === '-') {
                if (strpos($arg, 'v') !== false) $this->verbose = true;
                if (strpos($arg, 'p') !== false) $this->push = true;
                if (strpos($arg, 's') !== false) $this->stash = true;
                if (strpos($arg, 'r') !== false) $this->rebase = true;
            } else {
                $this->message = $arg;
            }
        }
    }
}

class Cmd
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function exec($command)
    {
        $branch = getCurrentBranch();

        $out = printLn(yellow($branch . '> ' . $command));
        if ($this->config->verbose) {
            echo $out;
        }
        $out = array_filter(explode("\n", shell_exec($command)));

        var_dump($out);
    }
}

# hotfix -ps
# -v verbose - показывать вывод гита
# -p push    - сразу запушить изменения
# -s stash   - если изменения сделаны заранее, использовать stash, чтобы добавить их (при хотфиксе - поведение по умолчанию)
# -r rebase  - склеить коммиты и сребейзить на develop

$conf = new Config($argv);
$conf->hotfixBranch = getHotfixBranch();

//hotfixCheckReqs($conf);

$cmd = new Cmd($conf);

$nextTag = getNextTag();
if ($conf->stash) {
    $cmd->exec('git stash');
}
