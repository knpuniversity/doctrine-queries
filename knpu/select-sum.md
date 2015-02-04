# SELECT the SUM (or COUNT)

In every query so far, Doctrine gives us objects. That's its default mode,
but we can also easily use it to select specific fields.

On our category page, you can see how many of each fortune has been printed
over time. At the top, let's total those numbers with a SUM() query and print
it out.

In `showCategoryAction()`, create a new variable - `$fortunesPrinted`, that'll
be a number. And of course, we'll write a new query to get this. But instead
of shoving this into `CategoryRepository`, this queries the `FortuneCookie`
entity, so we'll use its repository instead. So, `AppBundle:FortuneCookie`,
and we'll call a new `countNumberPrintedForCategory` method. Pass the `$category`
object as an argument:

[[[ code('a2118ad146') ]]]

This will return the raw, summed number. To actually use this, pass this
into the template:

[[[ code('f2253c98b7') ]]]

And now print it out: `{{ fortunesPrinted }}`, put that through Twig's
`number_format` filter and add the word "total":

[[[ code('5b6541fde1') ]]]

Amazing. You already know the next step: we need to create a new
`countNumberPrintedForCategory` method inside of `FortuneCookieRepository`
and make it query not for an object, but just a single number: the sum of
how many times each fortune has been printed. That means we'll be totaling
the `numberPrinted` property on `FortuneCookie`:

[[[ code('0912879914') ]]]

Open `FortuneCookieRepository` and add the new public function. We're expecting
a `Category` object, so I'll type-hint the argument like a nice, respectable
programmer. Every query starts the same: `$this->createQueryBuilder()` and
we'll use `fc` as the alias. Keep the alias consistent for an entity, it'll
save you heartache later.

Next, we need an `andWhere()` because we need to only find `FortuneCookie`
results for this Category. So, `fc.category` - because `category` is the
name of the property on `FortuneCookie` for the relationship. Now, equals
`:category`. And next, we'll set that parameter:

[[[ code('d7bbb7a7fe') ]]]

This looks like every query we've made before, and if we finish now, it'll
return `FortuneCookie` objects. That's lame - I want just the sum number.
To do this, call `select()`. Nope, this is *not* `addSelect` like we used
before. When we call `createQueryBuilder` from inside `FortuneCookieRepository`,
the query builder has a `->select('fc')` built into it. In other words, it's
selecting everything from `FortuneCookie`. Calling `select()` clears out
anything that's being selected and replaces it with our
`SUM(fc.numberPrinted) as fortunesPrinted`:

[[[ code('080fbb15a2') ]]]

We're giving the value an alias, just like you can do in SQL. Now, instead
of an object, we're getting back a single field. Let's finish it! Add `getQuery()`.

Last, should we call `execute()` or `getOneOrNullResult()`? If you think
about the query in SQL, this will return a single row that has the `fortunesPrinted`
value. So we want to return just one result - use `getOneOrNullResult()`:

[[[ code('e4c9be2648') ]]]

Love it! I'm curious to see what this query returns, so let's `var_dump`
`$fortunesPrinted` inside our controller:

[[[ code('ba7647f121') ]]]

Refresh! It's just what you'd expect: an array with a single key called `fortunesPrinted`.
So the `$fortunesPrinted` variable isn't *quite* a number - it's this array
with a key on it. But let me show you a trick. I told you about `execute()`
and `getOneOrNullResult()`: the first returns many results, the second returns
a single result or null. But if you're returning a single row that has only
a single column, instead of `getOneOrNullResult()`, you can say `getSingleScalarResult()`:

[[[ code('a18eed4975') ]]]

This says: ok, you're only returning one row with one column, let me just
give you that value directly. This is really handy for SUMs and COUNTs.

Refresh! Hey, we have *just* the number! Time to celebrate - take out the
`var_dump`, refresh and... great success! So not only can we select specific
fields instead of getting back objects, if you're selecting just one field
on one row, `getSingleScalarResult()` is your new friend.


