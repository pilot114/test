<?php

include './utils.php';

# hotfix -ps
# -v verbose - показывать вывод гита
# -p push    - сразу запушить изменения
# -s stash   - если изменения сделаны заранее, использовать stash, чтобы добавить их (при хотфиксе - поведение по умолчанию)
# -r rebase  - склеить коммиты и сребейзить на develop

$conf = new Config($argv);
$conf->hotfixBranch = getHotfixBranch();

//hotfixCheckReqs($conf);

$cmd = new Cmd($conf);

if ($conf->stash) {
    echo printLn(blue("Прячем изменения..."));
    $cmd->exec('git stash');
}

echo printLn(blue("Проверяем актуальность веток..."));
$cmd->exec('git checkout develop && git pull origin develop && git fetch --tags');
$cmd->exec('git checkout master && git pull origin master && git fetch --tags');

$nextTag = getNextTag();
echo printLn(blue("Новый хотфикс: $nextTag"));

if ($conf->stash) {
    echo printLn(blue("Применяем изменения..."));
    $cmd->exec('git stash apply');
}
echo printLn(blue("Коммитим изменения: $conf->message"));
