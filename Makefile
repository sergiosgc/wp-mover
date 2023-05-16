all: categories

media:
	./sync_media

posts:
	./sync_posts posts

translations:
	./sync_translations posts

featured_image:
	./sync_featured_image posts

categories:
	./sync_categories posts
