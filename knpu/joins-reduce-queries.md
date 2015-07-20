# Joins and addSelect Reduce Queries

There's a problem with our page! Sorry, I'll stop panicking - it's not a
*huge* deal, but if you look at the bottom, two queries are being executed.
That's strange: the only query I remember making is inside my `FortuneController`
when we call `search()`.

Click the web debug toolbar to see what the queries are. Ah, the first I
recognize: that's our search query. But the second one is something different.
Look closely: it's querying for all the `fortune_cookie` rows that are related
to this category:

```
SELECT t0.* FROM fortune_cookie t0
    WHERE t0.category_id = 4;
```

If you've heard about "lazy loading" of relationships, you probably know
what this comes from. The query is actually coming from our template. We
loop over the array of Category object, and then print `category.fortuneCookies|length`:

[[[ code('6cfee977aa') ]]]

The `Category` object has *all* of the data for itself, but at this point,
it hasn't yet fetched the data of the FortuneCookie's that it's related to
in the database. So at the moment we call `category.fortuneCookies` and try
to count the results, it goes out and does a query for all of the `fortune_cookie`
rows for this category. That's called a lazy query, because it was lazy and
waited to be executed until we actually needed that data.

This "extra query" isn't the end of the world. In fact, I don't usually fix
it until I'm working on my page's performance. On the homepage without a
search, it's even *more* noticeable. We have *7* queries here: one for the
categories, and one extra query to get the fortune cookies for *each* category
in the list. That makes 2, 3, 4, 5, 6 and 7 queries. This is a classic problem
called the *n+1 problem*.

And again, it's not the end of the world - so don't over-optimize. But let's
fix it here.

## Reducing Queries with addSelect

Back in `CategoryRepository`, once we've joined over to our `fortuneCookies`,
we can say `->addSelect('fc')`:

[[[ code('486d225972') ]]]

And just by doing that, our second query is gone! It's black magic - don't
worry about how it works! You know I'm kidding, here's the deal. Remember
that when we call `$this->createQueryBuilder()` from inside a repository
class, that automatically selects everything from the `Category`. So it's
equivalent to calling `->select('cat')`. Calling `addSelect()` means that
we're going to select all the Category information *and* all the FortuneCookie
information.

## addSelect and the Return Value

There's one super-important thing to keep in mind: even though we're selecting
more data, this function returns the *exact* same thing it did before: an
array of `Category` objects. That's different from SQL, where selecting all
the fields from a joined table will give you more fields in your result.
Here, `addSelect()` just tells Doctrine to fetch the `FortuneCookie` data,
but store it internally. Later, when we access the `FortuneCookie`s for a
`Category`, it's smart enough to know that it doesn't need that second query.
So we can reduce the number of queries used without needing to go update
any other code: this function still returns an array of Categories.

## Adding addSelect to findAllOrdered()

Go back to the homepage without a search. Dang, we still have those 7 ugly
queries. And that's because this uses a different method: `findAllOrdered`.
Let's to the same thing here. `->leftJoin('cat.fortuneCookies', 'fc')` and
then an `addSelect('fc')`:

[[[ code('5f56ad199c') ]]]

Our two queries start to have some duplication. That's an issue we'll fix later.
We're hoping to see our 7 queries drop *all* the way to 1 - the one query
for all of the Categories. Perfect!

## Adding addSelect to find()

We're on a roll! Click into a category - like Proverbs. Here, we have *two*
queries. This is the same problem - query #1 is the one we're doing in our
controller. Query #2 comes lazily from the template, where we're looping
over all the fortune cookies for the category.

We're using the built-in `find()` method in the controller:

[[[ code('4d18844a05') ]]]

But since it doesn't let us do any joins, we need to do something more custom.
Call a new method `findWithFortunesJoin`. You know the drill: we'll go
into `CategoryRepository` and then add that method. And at this point, this
should be a really easy query. I'll copy the `search()` function, then simplify
things in the `andWhere`: `cat.id = :id`. We want to keep the `leftJoin()`
and the `addSelect` to remove the extra query. Update `setParameter` to set
the `id` placeholder:

[[[ code('d319f5aa21') ]]]

The `execute()` function returns an array of results, but in this case, we
want just *one* `Category` object, or null if there isn't one. So we'll use
the other function I talked about to finish the query: `getOneOrNullResult()`.

Refresh! Two queries is now 1.

Exactly like in SQL, JOINs have two purposes. Sometimes you JOIN because
you want to add a WHERE clause or an ORDER BY on the data on that JOIN'ed
table. The second reason to JOIN is that you *actually* want to SELECT data
from the table. In Doctrine, this second reason feels a little different because
even though we're SELECTing from the `fortune_cookie` table, the query still
returns the same array of `Category` objects as before. But Doctrine has
that extra data in the background. 

But this doesn't *have* to be the case. Over the next two chapters, we'll
start SELECT'ing individual fields, instead of entire objects.
