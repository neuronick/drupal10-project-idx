<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class ScriptHandler {

  public static function createSymLinks(Event $event) {
    $fs = new Filesystem();
    $composerRoot = getcwd();
    $drupalRoot = self::getDrupalRoot($event);

    $config_files = [
      [
        'source' => $composerRoot . '/src/modules',
        'target' => $drupalRoot . '/modules/custom',
      ],
      [
        'source' => $composerRoot . '/src/themes',
        'target' => $drupalRoot . '/themes/custom',
      ],
      [
        'source' => $composerRoot . '/src/profiles',
        'target' => $drupalRoot . '/profiles/custom',
      ],
      [
        'source' => $composerRoot . '/src/sites',
        'target' => $drupalRoot . '/sites',
      ],        
    ];
    
    foreach ($config_files as $config_file) {
      if ($fs->exists($config_file['source'])) {
        self::createOrUpdateSymlink($fs, $config_file['source'], $config_file['target']);
      }
    }
  }

  private static function getDrupalRoot(Event $event): string {
    $extras = $event->getComposer()->getPackage()->getExtra();
    
    if (isset($extras['drupal-web-dir'])) {
      return getcwd() . '/' . $extras['drupal-web-dir'];
    }
    
    // Default to 'web' if not specified
    return getcwd() . '/web';
  }

  private static function createOrUpdateSymlink(Filesystem $fs, string $source, string $target) {
    // Check if the target doesn't exist or is not a symlink
    if (!$fs->exists($target) || !is_link($target)) {
      // Remove the target if it exists (but is not a symlink)
      if ($fs->exists($target)) {
        try {
          $fs->remove($target);
        } catch (IOException $e) {
          echo "Warning: Unable to remove existing directory at $target. Skipping symlink creation.\n";
          echo "Error: " . $e->getMessage() . "\n";
          return;
        }
      }
      // Create the symlink
      try {
        $fs->symlink($source, $target);
        echo "Created symlink: $target -> $source\n";
      } catch (IOException $e) {
        echo "Error: Unable to create symlink from $source to $target.\n";
        echo "Error: " . $e->getMessage() . "\n";
      }
    }
    else {
      // Check if the existing symlink points to the correct location
      $currentLink = readlink($target);
      if ($currentLink !== $source) {
        // Remove the incorrect symlink and create a new one
        try {
          $fs->remove($target);
          $fs->symlink($source, $target);
          echo "Updated symlink: $target -> $source\n";
        } catch (IOException $e) {
          echo "Error: Unable to update symlink from $source to $target.\n";
          echo "Error: " . $e->getMessage() . "\n";
        }
      }
      else {
        echo "Symlink already exists and is correct: $target -> $source\n";
      }
    }
  }
}