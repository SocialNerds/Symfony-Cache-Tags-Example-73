<?php
// Get composer libraries.
require_once 'vendor/autoload.php';

// Include Redis cache adapter in order to use Redis as cache
// repository.
use Symfony\Component\Cache\Adapter\RedisAdapter;

// Inlude TagAwareAdapter to make Redis cache work with cache tags.
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

// Initialize connection to Redis server.
$redisClient = RedisAdapter::createConnection('redis://redis:6379');

// Use Redis connection to initialize cache adapter. From now on
// we can use Redis as cache.
$redisCache = new RedisAdapter($redisClient);

// Add ability to our cache adapter to use tags for cache invalidation.
$cache = new TagAwareAdapter($redisCache);

// Get authors data (e.g. from DB) and save them to the corresponding variables.
$author1 = 'Thanos<br>';
$author2 = 'Nokas<br>';

// Save authors to cache with their appropriate cache tags.
$author1Cache = $cache->getItem('author_1');
$author1Cache->set($author1);
$author1Cache->tag(['author_tag_1']);
$cache->save($author1Cache);
$author2Cache = $cache->getItem('author_2');
$author2Cache->set($author2);
$author2Cache->tag(['author_tag_2']);
$cache->save($author2Cache);

// Get categories data (e.g. from DB) and save them to the corresponding variables.
$category1 = 'Science<br>';
$category2 = 'Programming<br>';

// Save categories to cache with their appropriate cache tags.
$category1Cache = $cache->getItem('category_1');
$category1Cache->set($category1);
$category1Cache->tag(['category_tag_1']);
$cache->save($category1Cache);
$category2Cache = $cache->getItem('category_2');
$category2Cache->set($category2);
$category2Cache->tag(['category_tag_2']);
$cache->save($category2Cache);

// Get articles data (e.g. from DB) and save them to the corresponding variables.
$article1 = 'This is article 1. I know, it\'s not just a string.<br>' . $author1 . $category1;
$article2 = 'This is article 2. I know, it\'s not just a string.<br>' . $author2 . $category2;

// Save articles to cache with their appropriate cache tags.
$article1Cache = $cache->getItem('article_1');
$article1Cache->set($article1);
$article1Cache->tag(['article_tag_1', 'category_tag_1', 'author_tag_1']);
$cache->save($article1Cache);
$article2Cache = $cache->getItem('article_2');
$article2Cache->set($article2);
$article2Cache->tag(['article_tag_2', 'category_tag_2', 'author_tag_2']);
$cache->save($article2Cache);

// Show all articles from cache.
echo ('<h3>Show all articles from cache,</h3>');
$articleCache = $cache->getItem('article_1');
echo ($articleCache->get());
$articleCache = $cache->getItem('article_2');
echo ($articleCache->get());

// Now, let's say that category 1 changes (Science), and we invalidate its cache
// through its cache tag.
echo ('<h3>Invalidating cache tag \'category_tag_1\'</h3>');
$cache->invalidateTags(['category_tag_1']);

// Then, every cache item, that is depending on category 1, will instantly invalidate.
echo ('<h3>Checking article 1 cache. It should be invalidated.</h3>');
$articleCache = $cache->getItem('article_1');
if (!$articleCache->isHit()) {
    echo ('Article 1 no longer in cache.' . '<br>');
}
