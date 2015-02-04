# JOINs!

I love JOINs. I do! I mean, a query isn't *truly* interesting unless you're
joining across tables to do some query Kung fu. Doctrine makes JOINs *really*
easy - it's one of my favorite features! Heck, they're *so* easy that I think
it confuses people. Let me show you.

Right now, our search matches fields on the Category, but it *doesn't* match
any of the fortunes in that Category. So if we search for `cat`, we get the
no-results frowny face. Time to fix it!

## LEFT JOIN in SQL

The query for this page is built in the `search()` function. Let's think
about what we need in SQL first. That query would select FROM `category`, but
with a `LEFT JOIN` over to the `fortune_cookie` table ON
`fortune_cookie.categoryId = category.id`. Once we have the `LEFT JOIN` in
normal SQL land, we can add a WHERE statement to search on any column in the
`fortune_cookie` table.

```
SELECT cat.* FROM category cat
    LEFT JOIN fortune_cookie fc ON fc.categoryId = cat.id
    WHERE fc.fortune LIKE '%cat%';
```

## ManyToOne and OneToMany Mapping

In Doctrine-entity-land, all the relationships are setup. The `FortuneCookie`
has a `ManyToOne` relationship on a `category` property:

[[[ code('de9d7674eb') ]]]

And inside `Category`, we have the
[inverse](https://knpuniversity.com/screencast/symfony2-ep3/doctrine-inverse-relation)
side of the relationship: a `OneToMany` on a property called `fortuneCookies`:

[[[ code('4975eccc5f') ]]]

Mapping this side of the relationship is optional, but *we'll* need it to
do our query.

## Adding the leftJoin Query

Let's go add our LEFT JOIN to the query builder! If you're thinking there's
a `leftJoin` method, winner! And this time, we *are* going to use it. Join
on `cat.fortuneCookies`. Why `fortuneCookies`? Because this is the *name*
of the property on `Category` for this relationship.

The second argument to `leftJoin()` is the alias we want to give to `FortuneCookie`,
`fc`::

[[[ code('754262e346') ]]]

And right away, we can see why Doctrine JOINs are so easy, I mean confusing,
I mean easy. This is *all* we need for a JOIN - we don't have any of the
LEFT JOIN ON `fortune_cookie.categoryId = category.id` kind of stuff. Sure,
this will be in the final query, but *we* don't need to worry about that
stuff because Doctrine already knows how to join across this relationship:
all the details it needs are in the relationship's annotations.

The `cat.fortuneCookies` thing *only* works because we have the `fortuneCookies`
OneToMany side of the relationship. Adding this mapping for the inverse side
is optional, but if we didn't have it, we'd need to add it right now: our
query depends on it.

LEFT JOIN, check! And just like normal SQL, we can use the `fc` alias from
the joined table to update the WHERE clause. I'll break this onto multiple
lines for my personal sanity and then add `OR fc.fortune LIKE :searchTerm`
because `fortune` is the name of the property on `FortuneCookie` that holds
the message:

[[[ code('2b087bf3b2') ]]]

Moment of truth! We've got a match! Our fortunes are being searched.

## JOINing, but Querying for the same Data

Even though we now have a LEFT JOIN, the result of the query is no different:
it still returns an array of `Category` objects. We *can* and *will* do some
JOINs in the future that actually *select* data from the joined table. But
if all you do is JOIN like we're doing here, it doesn't change the data that's
returned.
