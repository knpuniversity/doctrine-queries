# Reusing Queries with the Query Builder

Enough with all this SQL stuff. Remember the query builder I was raving about
earlier? I promised that one of its benefits is that, with a query builder,
you can re-use parts of a query. But we don't have any of that right now.

Open up `CategoryRepository`. We have three methods, and *all* of them repeat
the same `leftJoin()` to `cat.fortuneCookies` and the `addSelect()`:

[[[ code('3265eb127e') ]]]

Ah, duplication! When you see duplication like this - whether it's a WHERE,
an ORDER BY or a JOIN - there's a simple solution. Just add a new private
function and have *it* add this stuff to the query builder.

## Query-modifying Functions

Create a private function called `addFortuneCookieJoinAndSelect()`, because
that's what it's going to do! It'll accept a `QueryBuilder` as an argument.
Our goal is to, well, add the join to that. So I'll copy the 2 pieces that
we want, add a `$qb`, then paste it there. And just for convenience, let's
return this too:

[[[ code('3c6f204f9b') ]]]

So, our function takes in a `QueryBuilder`, it modifies it, then it returns
it so we can make any more changes. I'll be a *nice* programmer and add some
PHPDoc.

## Calling those Functions

The `findAllOrdered()` function is the one that fuels the homepage. So let's
start here! Get rid of that duplicated `leftJoin` and `addSelect`. Instead,
just call `$this->addFortuneCookieJoinAndSelect()` and pass it the `$qb`.
So *we* create the query builder, do some things with it, but let our new
function take care of the join stuff.

[[[ code('bfec09cd53') ]]]

This *should* give us the exact same results. But you should never believe
me, so let's go back and refresh the homepage. Yep, nice!

Now we get to celebrate by removing the rest of the duplication. So, `addSelect`
and `leftJoin` should be gone. Instead of returning the result directly,
we need to get a QueryBuilder first. So put `$qb =` in front and move the
`getQuery()` stuff down and put the `return` in front of it. In the middle,
call `addFortuneCookieJoinAndSelect()` like before:

[[[ code('0e8b002eb1') ]]]

And one more time in `findWithFortunesJoin()`. Remove the duplication, create
a `$qb` variable, return the last part of the query, and stick our magic
line in the middle::

[[[ code('2cbb441856') ]]]

Try it! Refresh and click into a category. It all works. And you know, I feel
a lot better. If there's one things I don't want to duplicate, it's query
logic. I hope this looks really obvious to you - it's just a simple coding
technique. But it's kind of amazing, because it's not something you can do
easily with string queries. And it can *really* save you if once you've got
complex WHERE clauses that need to be re-used. You don't want to screw that
stuff up.
