# To learn more about how to use Nix to configure your environment
# see: https://developers.google.com/idx/guides/customize-idx-env
{pkgs}: {
  # Which nixpkgs channel to use.
  channel = "stable-23.11"; # or "unstable"
  # Use https://search.nixos.org/packages to find packages
  packages = [
    pkgs.php83
    pkgs.php83Packages.composer
    pkgs.nodejs_20
  ];
  # Sets environment variables in the workspace
  env = {};
  idx = {
    # Search for the extensions you want on https://open-vsx.org/ and use "publisher.id"
    extensions = [
      "devsense.composer-php-vscode"
      "bmewburn.vscode-intelephense-client"
      "andrewdavidblum.drupal-smart-snippets"
      "mblode.twig-language"
      "redhat.vscode-yaml"
      "xdebug.php-debug"
      "neilbrayfield.php-docblocker"
      "esbenp.prettier-vscode"
      "ValeryanM.vscode-phpsab"
      "MehediDracula.php-namespace-resolver"
    ];
    workspace = {
      # Runs when a workspace is first created with this `dev.nix` file
      onCreate = {
        # Run composer install on workspace creation
        composer-install = "composer install";
        # Open editors for the following files by default, if they exist:
        default.openFiles = [ "README.md" ];
      };
      onStart = {
        # Run composer install on workspace creation
        composer-install = "composer install"; 
        # Open editors for the following files by default, if they exist:
        default.openFiles = [ "README.md" ];               
      };
    };
    # Enable previews and customize configuration
    previews = {
      enable = true;
        previews = {
          web = {
            command = ["php" "-S" "0.0.0.0:$PORT" "-t" "web"];
            manager = "web";
          };
        }; 
    };
  };
}
