= WP Mover =
WP Mover allows you to copy contents between WordPress installs. It ignores content that was already copied. It is useful in the scenario where a site is forked for upgrade/redesign and, pre-launch, changes to the live site need to be sent to the development version.

The scripts support custom post types and custom fields created using the Toolset plugin. They should be easily adaptable to other custom type plugins.

The scripts support and copy translations created using the WPML plugin.

== Usage Checklist == 

The origin WordPress install is considered the left site, the copy destination WordPress install is considered the right site.

1. On both left and right, enable the REST API on Toolset | Settings | Custom Content;
2. On both left and right, install the Code Snippets plugin and create the snippets defined in directories snippets.left and snippets.right;
3. Manually create users on the right site. Create a left_to_right_user_map.json file, with a dictionary; left id as key, right id as value. The left id may be a string (it must, JSON dict keys must be strings). The right id must be an int;
4. Create and translate categories on right. Categories are matched by the category slug, so make sure the slugs match between left and right;
5. Create API keys for an administrator account on left and right. Create a credentials.json file, with dictionary containing two keys: "left" and "right". Each of these is also a dictionary, containing "url", "username" and "password". Fill in the API key in the password entry;
6. Run sync_media. It produces left_to_right_media_map.json
7. Run sync_posts for each post type (at least ./sync_posts posts and ./sync_posts pages)

If the new install uses a different page builder (say, Elementor instead of Divi), pass --switch-builders to sync_posts. It will set the post content to be the builder-generated HTML instead of the builder source code.
