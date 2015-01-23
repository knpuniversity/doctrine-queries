# The QueryBuilder

Doctrine speaks DQL, even though it converts it eventually to SQL. But actually,
I don't write a lot of DQL. Instead, I use the `QueryBuilder`: an object
that helps you build a DQL string. The `QueryBuilder` is one of my favorite
parts of Doctrine.

## Creating the Query Builder

Let's comment out the `$dql` stuff. To create a `QueryBuilder`, create a
`$qb` variable and call `$this->createQueryBuilder()` from inside a repository.
Pass `cat` as the argument - this will be the alias to `Category`:

[[[ code('4dc71ddb3c') ]]]

## Building the Query

Now, let's chain some awesomeness! The `QueryBuilder` has methods on it like
`andWhere`, `leftJoin` and `addOrderBy`. Let's use that - pass `cat.name`
as the first argument and `DESC` as the second:

[[[ code('115057cbed') ]]]

This builds the exact same DQL query we had before. Because we're inside
of the `CategoryRepository`, the `createQueryBuilder()` function automatically
configures itself to select from the `Category` entity, using `cat` as the
alias.

To get a `Query` object from this, say `$qb->getQuery()`:

[[[ code('49ad83d6ba') ]]]

Wow.

Remember how we printed the SQL of a query? We can also print the DQL. So
let's see how our hard work translates into DQL:

[[[ code('c7278533e7') ]]]

Refresh! Look closely:

```
SELECT cat FROM AppBundle\Entity\Category ORDER BY cat.name DESC
```

That's character-by-character the *exact* same DQL that we wrote before.
So the query builder is just a *nice* way to help write DQL, and I prefer
it because I get method auto-completion and it can help you re-use pieces
of a query, like a complex JOIN, across multiple queries. I'll show you
that later.

Remove the `die` statement and refresh to make sure it's working:

[[[ code('5cc92cd6fc') ]]]

It looks perfect. To know more about the `QueryBuilder`, you can either keep
watching (that's recommended) or use your IDE to see all the different methods
the class has. But you should just keep watching.
