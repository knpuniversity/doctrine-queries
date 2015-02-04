# Selecting Specific Fields

We're on a roll! Let's select more fields - like an average of the `numberPrinted`
*and* the name of the category, all in one query. Yea yea, we *already* have
the Category's name over here - we're querying for the entire Category object.
But just stick with me - it makes for a good example.

Head back to `FortuneCookieRepository`. As I hope you're guessing, SELECTing
more fields is just like SQL: add a comma and get them. You could also use
the `addSelect()` function if you want to get fancy.

Add, `AVG(fc.numberPrinted)` and give that an alias - `fortunesAverage`. I'm
just making that up. Let's *also* grab `cat.name` - the name of the category
that we're using here:

[[[ code('bb19cb25a3') ]]]

I don't trust myself. So, `var_dump($fortunesPrinted)` in the controller
with our trusty `die` statement:

[[[ code('c909b38b4d') ]]]

Refresh! Uh oh, that's an awesome error:

    [Semantical Error] line 0, col 88 near 'cat.name FROM': Error 'cat'
    is not defined.

## Debugging Bad DQL Queries

This is what it looks like when you mess up your DQL. Doctrine does a really
good job of lexing and parsing the DQL you give it, so when you make a mistake,
it'll give you a pretty detailed error. Here, `cat is not defined` is because
our query references `cat` with `cat.name`, but I haven't made any JOINs
to create a `cat` alias. `cat` is not defined.

But real quick - go back to the error. If you scroll down the stack trace
a little, you'll eventually see the full query:

    SELECT SUM(fc.numberPrinted) as fortunesPrinted,
        AVG(fc.numberPrinted) fortunesAverage,
        cat.name
        FROM AppBundle\Entity\FortuneCookie fc
        WHERE fc.category = :category

For me, sometimes the top error is so small, it doesn't make sense. But
if I look at it in context of the full query, it's a lot easier to figure
out what mistake I made. 

Fixing our error is easy: we need to add a JOIN - this time an `innerJoin()`.
So, `innerJoin('fc.category', 'cat')`:

[[[ code('98c7875729') ]]]

Why `fc.category`? Because in the `FortuneCookie` entity, we have a `category`
property. That's how it knows which relationship we're talking about. So
`cat` is now aliased! Let's try again.

Ooook, another error: `NonUniqueResultException`. We're still finishing the
query with `getSingleScalarResult()`. But now that we're returning multiple
columns of data, it doesn't make sense anymore. The `NonUniqueResultException`
means that you either have this situation, or, more commonly, you're using
`getOneOrNullResult()`, but your query is returning mulitple rows. Watch
out for that.

Change the query to `getOneOrNullResult()`: the query still returns only
one row, but multiple columns:

[[[ code('74641b1891') ]]]

Refresh! Beautiful! The result is an associative array with `fortunesPrinted`,
`fortunesAverage` and `name` keys. And notice, we didn't give the category
name an alias in the query - we didn't say `as something`, so it just used
name by default:

[[[ code('a8913ee2e4') ]]]

And hey, I was even a bit messy: for the sum I said `as fortunesPrinted`
but for the average, I just said `fortunesAverage` with the `as`. The `as`
is optional - I didn't leave it out on purpose, but hey, good learning moment.

The query is beautiful, so let's actually use our data. In the controller,
change the result from `$fortunesPrinted` to `$fortunesData` - it's really
an array. And below, set `$fortunesPrinted` to `$fortunesData['...']`. I'll
check my query to remember the alias - it's `fortunesPrinted`, so I'll use
that. I'll do the same thing for the other two fields:

[[[ code('094c97162c') ]]]

The alias for the average is `fortunesAverage`. And the last one just uses
`name`. Let's pass these into the template:

[[[ code('3c4bc12883') ]]]

And again, I know, the `categoryName` is redundant - we already have the
whole category object. But to prove things, use `categoryName` in the template.
And below, add an extra line after the total and print `averagePrinted`:

[[[ code('0515d12ae3') ]]]

Moment of truth! Woot! 244,829 total, 81,610 average, and the category name
still prints out. Doctrine normally queries for objects, and that's great!
But remember, nothing stops you from using that `select()` function to say:
no no no: I don't want to select objects anymore, I want to select specific
fields.


