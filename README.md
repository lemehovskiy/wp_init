# Install

## 1. Clone repository into your projects folder

#### In result you should have structure like this:
     .
     ├── ...
     ├── My_projects
     │   ├── Project_1
     │   ├── Project_2
     │   ├── Project_3
     │   └── wp_init
     └── ...

## 2. Create plugins folder in wp_init/wp-init-src and put there your premium plugins

#### In result you should have structure like this:
     .
     ├── ...
     ├── My_projects
     │   ├── Project_1
     │   ├── Project_2
     │   ├── Project_3
     │   └── wp_init
     │       └── wp-init-src
     │           ├── ...
     │           ├── plugins
     │           │   ├── acf-repeater
     │           │   └── advanced-custom-fields-pro
     │           └── ...
     └── ...

# Usage

### 1. Open terminal and change the current working directory to the wp_init folder

### 2. Initialize new project

```sh
php wp-init.php --init PROJECT_SLUG
```

###### For example:

```sh
php wp-init.php --init my-new-wp-project
```

#### In result you should have structure like this:
     .
     ├── ...
     ├── My_projects
     │   ├── my-new-wp-poject
     │   ├── Project-1
     │   ├── Project-2
     │   ├── Project-3
     │   └── wp_init
     └── ...

### 3. Open wp-init-config.json in your initialized folder my-new-wp-project

#### 1. Fill "project_name"

###### For example:
```json
  {
      "project_name": "My new wp project"
  }
```

#### 2. Fill local plugins

###### For example:
```json
  {
      "local-plugins": {
          "acf-repeater": {
            "path": "wp-init-src/plugins/acf-repeater",
            "install": true
          },
          "advanced-custom-fields-pro": {
            "path": "wp-init-src/plugins/advanced-custom-fields-pro",
            "install": true
          }
      }
  }
```

#### 3. Fill remote plugins

###### For example:
```json
  {
      "remote-plugins": {
          "contact-form-7": {
            "url": "https://downloads.wordpress.org/plugin/contact-form-7.4.9.zip",
            "install": true
          },
          "simple-custom-post-order": {
            "url": "https://downloads.wordpress.org/plugin/simple-custom-post-order.zip",
            "install": true
          }
      }
  }
```

#### 4. Remove wp core files

###### For example:
```json
  {
      "remove_wp_core_files": [
          "wp-content/themes/twentyfifteen",
          "wp-content/themes/twentyseventeen",
          "wp-content/themes/twentysixteen",
          "wp-content/plugins/akismet",
          "wp-content/plugins/hello.php"
      ]
  }
```

#### 5. Remove starter theme files

###### For example:
```json
  {
     "remove_starter_theme_files": [
         "core/post_types/sample.php",
         "core/taxonomies/sample.php",
         ".gitignore"
     ]
  }
```

#### 6. Register post types

###### For example:
```json
  {
      "post_types": [
         {
           "slug": "news",
           "name": "News",
           "singular_name": "News"
         },
          {
            "slug": "event",
            "name": "Events",
            "singular_name": "Event"
          }
      ]
  }
```

#### 7. Register taxonomies

###### For example:
```json
  {
      "taxonomies": [
        {
          "slug": "news-category",
          "name": "News categories",
          "singular_name": "News Category",
          "assign_to_post_type": "news"
        },
        {
          "slug": "event-category",
          "name": "Event categories",
          "singular_name": "Event category",
          "assign_to_post_type": "event"
        }
     ]
  }
```

#### 8. Gitignore rules

###### For example:
```json
  {
    "gitignore": [
      ".idea",
      "**/build",
      "**/node_modules",
      "**/DS_Store",
      "wp-config.php"
    ]
  }
```

#### 9. Flexible templates

###### For example:
```json
  {
    "flexible_templates": [
        {
          "name": "Page builder",
          "slug": "page_builder",
          "sections": [
            "intro_1",
            "intro_2",
            "special_slider"
          ]
        }
    ]
  }
```

### 4. Open terminal and change the current working directory to my-new-wp-project folder

#### 1. Run install

```sh
php wp-init.php --install
```

#### 2. Run destroy to clear project from wp_init files

```sh
php wp-init.php --destroy
```