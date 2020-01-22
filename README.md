# Booklet framework


## Install

Step 1: Create composer.json file in your new app directory with code.

```
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/boooklet/framework.git"
    },
    {
      "type": "git",
      "url": "https://github.com/boooklet/tester.git"
    }
  ],
  "require": {
    "boooklet/framework": "dev-master",
    "boooklet/tester": "dev-master"
  }
}
```

Step 2: Copy files from vendor/boooklet/framework/app_template to your app path

Step 3: Rename files \_.gitignore and \_.htaccess


## Tests

Setup test database: test_framework

```
php run_test.php db:prepare
```
