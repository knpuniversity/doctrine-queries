# And WHERE Or WHERE

The most common thing to do in a query is to add a WHERE clause. Unfortunately,
Doctrine doesn't support that. I'm kidding!

I have a search box - let's search for "Lucky Number". This isn't hooked
up yet, but it adds a query parameter `?q=lucky+number`. Let's use that to
only return categories matching that.

Back in `FortuneController`, add a `Request $request` argument to the controller.
Below, let's look to see if there is a `q` query parameter on the URL or not.
If there is, we'll search for it, otherwise, we'll keep finding all the categories.
For the search, call a new method on the repository called `search()`, and
pass in the term.

    CODE

Back in `CategoryRepository`, let's make that function:

    CODE

We'll make a QueryBuilder just like before, but do the entire query in just
one statement. Start by calling `createQueryBuilder()` and passing it `cat`.
The only thing our query needs is a WHERE clause to match the term to the
`name` of the Category. Let's chain!

## Building AND WHERE into a Query

Use a function called `andWhere()`. Don't worry - Doctrine won't add an
`AND` to the query, unless it's needed. Inside, write `cat.name = `. But
instead of passing the variable directly into the string like this, use a
placeholder. Type colon, then make up a term. On the next line, use `setParameter`
to tell Doctrine what I want to fill in for that term. So type `searchTerm`,
should be replaced with `$term`. This avoids SQL injection attacks, so don't
muck it up! Finally, we call `getQuery()` - like before - and `execute()`:

    CODE

And just like that, we should be able to go back, refresh, and there's our
"Lucky Number" category match. And on the homepage, we still see everything.

## Query with LIKE

But if we just search for "Lucky", we get nothing back because we're doing
an exact match. But just like with normal SQL, we know that's easy to fix.
And you already know how:  just change `=` to `LIKE` - *just* like SQL! It's
just like writing SQL people!

For the parameter value, surround it by percent signs to complete things.
Refresh! We've got a match!

## OR WHERE

What about adding an OR WHERE to the query? The `Category` entity has an
`iconKey` property, which is where we get this little bug icon. For "Lucky Number",
it's set to `fa-bug` from Font Awesome. Search for that. No results of course!

Let's update our query to match on the `name` OR `iconKey` property. If you're
guessing that there's an `orWhere()` method, you're right! If you're guessing
that I'm going to use it, you're wrong!

    CODE

The string inside of the `andWhere` is a mini-DQL expression. So you can
add ` OR cat.iconKey LIKE :searchTerm`. And the `searchTerm` placeholder
is already being filled in:

    CODE

Refresh! Another match! 

## Avoid orWhere() and where()

So even though there is an `orWhere()` function, don't use it - it can cause
WTF moments. Imagine if Category had an `enabled` property, and we built
a query like this:

    CODE

What would the SQL look like for this? Would it have the three WHERE clauses
in a row, or would it correctly surround the first two with parentheses?

    SELECT * FROM category WHERE
        name LIKE '%lucky%' OR iconKey LIKE '%lucky%' AND enabled = 1;

    SELECT * FROM category WHERE
        (name LIKE '%lucky%' OR iconKey LIKE '%lucky%') AND enabled = 1;

Doctrine does the second and the query works as expected. But it's a lot
less clear to read. Instead, think of each `andWhere()` as being surrounded
by its *own* parentheses, and put OR statements in there.

Oh, and there's also a `where()` function. Don't use it either - it removes
any previous WHERE clauses on the query, which you might be doing accidentally.

In other words, always use `andWhere()`, it keeps life simple.
