<?php

/**
 * packagexmlsetup.php for PEAR2_Autoload
 *
 * PHP version 5
 *
 * @category PEAR2
 * @package  PEAR2_Autoload
 * @author   Gregory Beaver <cellog@php.net>
 * @author   Brett Bieber <saltybeagle@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version  GIT: $Id$
 * @link     http://pear2.php.net/PEAR2_Autoload
 */

/**
 * References the package in $package and/or $compatible.
 */
use Pyrus\Developer\PackageFile\v2;

/**
 * Configuration array.
 * 
 * Each key is the task.
 * 
 * The task "replace" uses an array where the key is the value to be searched
 * for, and the value is an array of additional attributes for the task, which
 * normally contain at least "type" (pear-config/package-info) and "to", which
 * specifies the value to replace it with.
 * 
 * The task "eol" uses an array where the key is a filename pattern to be
 * matched, and the value is the target platform's EOL to be used for those
 * file names (windows/unix).
 * 
 * Unrecognized tasks are ignored.
 * 
 * @var array
 */
$config = array(
    'replace' => array(
        '../src' => array(
            'type' => 'pear-config',
            'to' => 'php_dir'
        ),
        'GIT: $Id$' => array(
            'type' => 'package-info',
            'to' => 'version'
        )
    ),
    'eol' => array()
);

if (!isset($package)) {
    die('This file must be executed via "pyrus.phar make".');
}

$packageGen = function (
    array $config,
    v2 $package,
    v2 $compatible = null
) {

    $tasksNs = $package->getTasksNs();
    $cTasksNs = $compatible ? $compatible->getTasksNs() : '';

    $oldCwd = getcwd();
    chdir(__DIR__);
    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            '.',
            RecursiveDirectoryIterator::UNIX_PATHS
            | RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::LEAVES_ONLY
    ) as $path) {
            $filename = substr($path->getPathname(), 2);
            $cFilename = str_replace('src/', 'php/', $filename);

        if (isset($package->files[$filename])) {
            $parsedFilename = pathinfo($filename);
            $as = (strpos($filename, 'examples/') === 0)
                ? $filename
                : substr($filename, strpos($filename, '/') + 1);
            if (strpos($filename, 'scripts/') === 0) {
                if (isset($parsedFilename['extension'])
                    && 'php' === $parsedFilename['extension']
                    && !is_file(
                        $parsedFilename['dirname'] . '/' .
                        $parsedFilename['filename']
                    )
                    && is_file(
                        $parsedFilename['dirname'] . '/' .
                        $parsedFilename['filename'] . '.bat'
                    )
                ) {
                    $as = substr($as, 0, -4);
                }
            }
            $package->getReleaseToInstall('php')->installAs($filename, $as);

            $contents = file_get_contents($filename);
            foreach ($config['replace'] as $from => $attribs) {
                if (strpos($contents, $from) !== false) {
                    $attribs['from'] = $from;
                    $package->files[$filename] = array_merge_recursive(
                        $package->files[$filename]->getArrayCopy(),
                        array(
                            "{$tasksNs}:replace" => array(
                                array(
                                    'attribs' => $attribs
                                )
                            )
                        )
                    );

                    if ($compatible) {
                        $compatible->files[$cFilename] = array_merge_recursive(
                            $compatible->files[$cFilename]->getArrayCopy(),
                            array(
                                "{$cTasksNs}:replace" => array(
                                    array(
                                        'attribs' => $attribs
                                    )
                                )
                            )
                        );
                    }
                }
            }

            foreach ($config['eol'] as $pattern => $platform) {
                if (fnmatch($pattern, $filename)) {
                    $package->files[$filename] = array_merge_recursive(
                        $package->files[$filename]->getArrayCopy(),
                        array(
                            "{$tasksNs}:{$platform}eol" => array()
                        )
                    );

                    if ($compatible) {
                        $compatible->files[$cFilename] = array_merge_recursive(
                            $compatible->files[$cFilename]->getArrayCopy(),
                            array(
                                "{$cTasksNs}:{$platform}eol" => array()
                            )
                        );
                    }
                }
            }
        }
    }
    chdir($oldCwd);
    return array($package, $compatible);
};

list($package, $compatible) = $packageGen(
    $config,
    $package,
    isset($compatible) ? $compatible : null
);
if (null === $compatible) {
    unset($compatible);
}
