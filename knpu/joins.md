# JOINs!

I love JOINs. I do! I mean, a query isn't *truly* interesting unless you're
joining across tables to do some query Kung fu. Doctrine makes JOINs *really*
easy - it's one of my favorite features! Heck, they're *so* easy that I think
it confuses people. Let me show you.

Right now, our search matches fields on the Category, but it *doesn't* match
any of the fortunes in that Category. So if we search for `cat`, we get the
no-results frowny face. Time to fix it!

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

In Doctrine-entity-land, all the relationships are setup. The `FortuneCookie`
has a `ManyToOne` relationship on a `category` property:



And inside `Category`,
we have the [inverse](https://knpuniversity.com/screencast/symfony2-ep3/doctrine-inverse-relation)
side of the relationship: a `OneToMany` on a property called `fortuneCookies`.
Mapping this side of the relationship is optional, but *we'll* need it to
do our query.

Let's go add our LEFT JOIN to the query builder! If you're thinking there's
a `leftJoin` method, winner! And this time, we *are* going to use it. Join
on `cat.fortuneCookie`.



Let's be honest, a query isn't really fun unless it has... a JOIN! 
