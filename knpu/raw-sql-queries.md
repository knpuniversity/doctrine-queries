# Raw SQL Queries

All this Doctrine entity object stuff and DQL queries are really great.
But if you ever feel overwhelmed with all of this or need write a *really*
complex query, you can always fall back to using raw SQL. Seriously, this
is *huge*. When Doctrine scares you, you are *totally* free to run away and
use plain SQL. I won't judge you - just get that feature finished and launch
it already. If a tool isn't helping you, don't use it.

Put on your DBA hat and let's write some SQL!

Open up `FortuneCookieRepository`. This is where we built a query that selected
a sum, an average and the category name.

## The DBAL Connection

The most important object in Doctrine is.... the entity manager! But that's
just a puppet for the *real* guy in charge: the connection! Grab it by getting
the entity manager and calling `getConnection()`. Let's `var_dump()` this:

[[[ code('862a15b226') ]]]

Head to the browser and click into one of the category pages. There's our
beautiful `var_dump()`. Hey! Look at the class name:

    Doctrine\DBAL\Connection

Fun fact! The Doctrine library is actually *multiple* libraries put together.
The two parts we care about are the ORM and the DBAL. The ORM is what does
all the magic mapping of data onto objects. The DBAL - or database abstraction
layer - can be used *completely* independent of the ORM. It's basically a
wrapper around PDO. Said in a less boring way, it's a library for executing
database queries.

So this DBAL Connection objects is *our* key to running raw database queries.
Google for "Doctrine DBAL Query" so we can follow its docs. Find the
[Data Retrieval And Manipulaton](http://doctrine-dbal.readthedocs.org/en/latest/reference/data-retrieval-and-manipulation.html)
section. Scroll down a little to a good example:

```php
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindValue(1, $id);
$stmt->execute();
```

This DBAL library is a really light wrapper around PHP's PDO. So if you've
used that before, you'll like this. But if not, it's like 3 steps, so stick
with me.

## Making a Raw SQL Query

Back in `FortuneCookieRepository`, let's write some simple SQL to test with:

    SELECT * FROM fortune_cookie;

When you use the DBAL, there are *no* entities and it doesn't know about
any of our Doctrine annotations. Yep, we're talking to the raw tables and
columns. So I used `fortune_cookies` because that's the name of the actual
table in the database.

Next, we'll use the SQL to get a statement. So:

    $stmt = $conn->prepare($sql);

And then we can `execute()` that, which runs the query but doesn't give you
the result. To get *that*, call `$stmt->fetchAll()` and `var_dump()` that:

[[[ code('0466f61d88') ]]]

Try it! And there it is: exactly what you'd expect with no effort at all.
It's literally the results - in array format - from the raw SQL query. Doctrine
isn't trying to hide this feature from you - just grab the Connection object
and you're dangerous.

## Prepared Statements

The query we made with the query builder is a bit more complex. Could we
replacle that with raw SQL? Sure! Well there's not really a good reason to
do this, since it's built and working. But let's prove we can do it!

Let's grab the "select" part of the query and stick that in our query. I
hate long lines, so let's use multiple. Piece by piece, add the other query
parts. The FROM is `fortune_cookie fc`. Add the `INNER JOIN` to `category`
`ON cat.id = fc.category_id`. And since we're in DBAL land, we don't have
any of our annotation mapping configuration, so we have to tell it exactly
how to join - it's just raw SQL. And for the same reason, we're using the
*real* column names, like `category_id`.

Add a single `WHERE` of `fc.category_id = :category`. That's some good-old-fashioned
boring SQL. I love it! The only thing we *still* need to do is fill in the
`:category` placeholder. Even though we're using the DBAL, we still don't
concatenate strings in our queries, unless you love SQL attacks or prefer
to live dangerously. Are you feeling lucky, punk?

Ahem. To give `:category` a value, just pass an array to `execute()` and
pass it a `category` key assigned to the id. Ok, done! Let's dump this!

[[[ code('b8589f71f1') ]]]

Boom! That's *exactly* what I was hoping for.

## Using fetch() to get back the First Row

Since our SQL gives us just *one* row, it'd be awesome to get just *its*
columns, instead of an array with one result. Just use `fetch()`!

[[[ code('b44ae643b4') ]]]

And now, this is exactly what our query builder gave us before. So get rid
of the `die()` statement and return the `fetch()` line:

[[[ code('92d0b99b00') ]]]

Just let the old code sit down there. Refresh! And we're prefectly back to
normal. Man, that was kinda easy. So if Doctrine ever looks hard or you're
still learning it, totally use SQL. It's no big deal.

## Native Queries?

One slightly confusing thing is that if you google for "doctrine raw sql",
you'll find a different solution - something called [NativeQuery](http://doctrine-orm.readthedocs.org/en/latest/reference/native-sql.html).
It sort of looks the same, just with some different function names. But there's
this `ResultSetMapping` thing. Huh. This `NativeQuery` thing allows you to
run a raw SQL query and then map that *back* to an object. That's pretty neat
I guess. But for me, if I'm writing some custom SQL, I'm fine just getting
back an array of data. I can deal with that. The `ResultSetMapping` confuses
me, and probably isn't worth the effort. But it's there if you want to geek
out on it.
