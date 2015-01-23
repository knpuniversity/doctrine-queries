# Doctrine DQL

Look, I know you already understand how to do queries in SQL - maybe you
dream of JOINs, orders and sub-queries. That's really dorky, but I get it.
But when you look at Doctrine, it's totally different - with its DQL and
its query builder, with their *own* ways of doing joins, and this hydration
of objects thing.

But did you know you can write native SQL queries in Doctrine? Yep! And you
can opt in or out of *any* of its features. Use more of them in one place,
where life is easier or when you're feeling like a Doctrine pro. Then go
simpler and use less when things get tough or you need to squeeze out ever
ounce of performance.

We'll learn about all of that. And don't worry - if you're good at SQL, you're
going to be great at writing queries in Doctrine.

## Query for Fortune Cookies

Our app is a Fortune Cookie inventory system. Yep, we've *finally* hit the
big time: working for a company that can tell you your future, wrapped up
inside a cheap cookie shell.

There are six different fortune categories that are loaded from the database.
And if you click on any of these, we see all of the fortunes for the category
and how many have been printed.

The project is a small Symfony app - but all the Doctrine stuff translates
to any app using the Doctrine ORM. We have 2 entities: `Category` and `FortuneCookie`:

[[[ code('f96c6255e4') ]]]

[[[ code('7611088470') ]]]

With a `ManyToOne` relation from `FortuneCookie` to the `Category`:

[[[ code('af27fc1d93') ]]]

For our homepage, we're using the entity manager to fetch the Category's
repository and call the built-in `findAll` function:

[[[ code('acc6bb39b5') ]]]

This returns *every* Category, and so far, it lets us be lazy and avoid
writing a custom query. The template loops over these and prints them out.
AMAZING.

## Doctrine Query Language (DQL)

Time to write a query! One that will order the categories alphabetically.
Call a new method called `findAllOrdered()`:

[[[ code('9b70c7dc8e') ]]]

This needs to live inside the `CategoryRepository` class. So create a
`public function findAllOrdered()`. To prove things are wired up, put a
`die` statement:

[[[ code('d364d0c3b9') ]]]

Refresh! Sweet, ugly black text - we're hooked up!

Ok, so *you're* used to writing SQL, maybe MySQL queries. Well, Doctrine
speaks a different language: DQL, or Doctrine Query Language. Don't worry
though, it's so close to SQL, most of the time you won't notice the difference.

Let's see some DQL. So: `$dql = 'SELECT cat FROM AppBundle\Entity\Category cat';`:

[[[ code('620e7537c8') ]]]

The big DQL difference is that instead of working with tables, you're working
with PHP classes. And that's why we're selecting from the full class name
of our entity. Symfony users are used to saying `AppBundle:Category`, but
that's just a shortcut alias - internally it always turns into the full class
name.

The `cat` part is an alias, just like SQL. And instead of `SELECT *`, you
write the alias - `SELECT cat`. This will query for every column. Later,
I'll show you how to query for only *some* fields.

## Executing DQL

To run this, we'll create a Query object. Get the `EntityManager`, call
`createQuery()` and pass it in the DQL. And once we have the `Query` object,
we can call `execute()` on it:

[[[ code('0f4d8d55b6') ]]]

This will return an array of `Category` *objects*. Doctrine's normal mode
is to always return *objects*, not an array of data. But we'll change that
later.

Let's query for some fortunes! Refresh the page. Nice - we see the exact same
results - this is what `findAll()` was doing in the background.

## Adding the ORDER BY

To add the `ORDER BY`, it looks just like SQL. Add `ORDER BY`, then `cat.name DESC`:

[[[ code('03e0930390') ]]]

Refresh! Alphabetical categories! So that's DQL: SQL where you mention class
names instead of table names. If you Google for "Doctrine DQL", you can find
a lot more in the [Doctrine docs](http://doctrine-orm.readthedocs.org/en/latest/reference/dql-doctrine-query-language.html),
including stuff like joins. 

## Show me the SQL!

Of course ultimately, Doctrine takes that DQL and turns it into a *real*
MySQL query, or PostgreSQL of whatever your engine is. Hmm, so could we *see*
this SQL? Well sure! And it might be useful for debugging. Just `var_dump`
`$query->getSQL()`:

[[[ code('ee8bdcccbb') ]]]

Refresh! It's not terribly pretty, but there it is. For all the coolness,
tried-and-true SQL lives behind the scenes. Remove that debug code.
