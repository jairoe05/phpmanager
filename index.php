<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Manager | Projects</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style type="text/css">
        body {font-family: "Ubuntu Mono", "Monospace", "Monaco", "Courier New"; font-size: 12px; padding-top: 70px}
        th, td {padding: 15px; text-align: center; border-collapse: collapse}
        tbody {width: 100%}
        table {border-collapse: collapse}
        table, th, td {border: 1px solid #8892bf}
        /* Search */
        .results tr[visible='false'] {display:none}
        .results tr[visible='true'] {display:table-row}
        /* Bootstrap */
        .navbar-default {background-color: #8892bf; border-color: #4f5b93}
        .navbar-default .navbar-brand {color: #ecf0f1}
        .navbar-default .navbar-brand:hover,
        .navbar-default .navbar-brand:focus {color: #ecdbff}
        .navbar-default .navbar-text {color: #ecf0f1}
        .navbar-default .navbar-nav > li > a {color: #ecf0f1}
        .navbar-default .navbar-nav > li > a:hover,
        .navbar-default .navbar-nav > li > a:focus {color: #ecdbff}
        .navbar-default .navbar-nav > .active > a,
        .navbar-default .navbar-nav > .active > a:hover,
        .navbar-default .navbar-nav > .active > a:focus {color: #ecdbff; background-color: #4f5b93}
        .navbar-default .navbar-nav > .open > a,
        .navbar-default .navbar-nav > .open > a:hover,
        .navbar-default .navbar-nav > .open > a:focus {color: #ecdbff; background-color: #4f5b93}
        .navbar-default .navbar-toggle {border-color: #4f5b93}
        .navbar-default .navbar-toggle:hover,
        .navbar-default .navbar-toggle:focus {background-color: #4f5b93}
        .navbar-default .navbar-toggle .icon-bar {background-color: #ecf0f1}
        .navbar-default .navbar-collapse,
        .navbar-default .navbar-form {border-color: #ecf0f1}
        .navbar-default .navbar-link {color: #ecf0f1}
        .navbar-default .navbar-link:hover {color: #ecdbff}

        @media (max-width: 767px) {
            .navbar-default .navbar-nav .open .dropdown-menu > li > a {color: #ecf0f1}
            .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
            .navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {color: #ecdbff}
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {color: #ecdbff;background-color: #4f5b93}
        }
    </style>
    <link rel="shortcut icon" href="http://php.net/favicon.ico" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                    <img src="http://php.net/favicon.ico" alt="">
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="">Projects</a>
                    </li>
                    <li>
                        <a href="./phpinfo.php">phpinfo</a>
                    </li>
                    <li>
                        <a href="./webconsole.php">Web Console</a>
                    </li>
                    <li>
                        <a href="http://github.com/kenpb/phpmanager" target="_blank">About</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                    $dir = new DirectoryIterator(dirname(__FILE__));
                    $types = [
                        'folder-close' => [],
                        'file'   => [],
                    ];

                    foreach ($dir as $fileinfo)
                    {
                        if ($fileinfo->isDot()) {
                            continue;
                        }

                        if (is_dir($fileinfo->getFilename())) {
                            $types['folder-close'][$fileinfo->getFilename()] = [$fileinfo->getOwner(), $fileinfo->getGroup(), $fileinfo->getPerms()];
                            
                            continue;
                        }
                        if ($fileinfo->getType = 'file') {
                            $types['file'][$fileinfo->getFilename()] = [$fileinfo->getOwner(), $fileinfo->getGroup(), $fileinfo->getPerms()];
                            continue;
                        }
                    }
                ?>
                <div class="panel panel-default">
                    <table class="table">
                        <thead style="background-color:#8892bf">
                            <tr>
                                <th>File name</th>
                                <th>Owner</th>
                                <th>Group</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody class="file-list">
                            <?php foreach ($types as $type => $content): ?>
                                <?php ksort($content); ?>
                                <?php foreach ($content as $name => $options): ?>
                                    <tr>
                                        <td style="background-color:rgb(240, 240, 240)">
                                            <a class="<?php echo $type ?>" href="<?php echo $name ?>">
                                                <span class="glyphicon glyphicon glyphicon-<?php echo $type ?>"></span>
                                                <?php echo $name ?>
                                            </a>
                                        </td>
                                        <?php
                                            list($owner, $group, $perms) = $options;
                                            $owner = posix_getpwuid($owner);
                                            $group = posix_getgrgid($group);
                                        ?>
                                        <td>
                                            <?php echo $owner['name'] ?>
                                        </td>
                                        <td>
                                            <?php echo $group['name']; ?>
                                        </td>
                                        <td>
                                            <?php echo substr(sprintf('%o', $perms), -4); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
