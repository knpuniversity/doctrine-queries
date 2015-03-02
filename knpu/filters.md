# Filters

I setup my fixtures so that about half of my FortuneCookies have a "discontinued"
value of true. For our startup fortune cookie company, that means we don't
make them anymore. But we're not showing this information anywhere on the
frontend yet.

But what if we wanted to *only* show fortune cookies on the site that we're
still making? In other words, where `discontinued` is false. Yes yes, I know.
This is easy. We could just go into `CategoryRepository` and add some `andWhere()`
calls in here for `fc.discontinued = true`.

But what if we wanted this WHERE clause to be added automatically, and everywhere
across the site? That's possible, and it's called a Doctrine Filter.

## Creating the Filter Class

Let's start by creating the filter class itself. Create a new directory
called `Doctrine`. There's no real reason for that, just keeping organized.
In there, create a new class called `DiscontinuedFilter`, and make sure we
put it in the right namespace:

[[[ code('17af1bef9b') ]]]

That's a nice blank class. To find out what goes inside, Google for
"Doctrine Filters" to get into their documentation. These filter classes
are simple: just extend the `SQLFilter` class, and that'll force us to have
one method. So let's do that - `extends SQLFilter`. My IDE is *angry* because
`SQLFilter` has an abstract method we need to add. I'll use PHPStorm's
Code->Generate shortcut and choose "Implement Methods". It does the work
of adding that `addFilterConstraint` method for me. And for some reason,
it's extra generous and gives me an extra `ClassMetadata` use statement,
so I'll take that out.

[[[ code('48e41dda81') ]]]

Ok, here's how this works. *If* this filter is enabled - and we'll talk about
that - the `addFilterConstraint()` method will be called on every query.
And this is *our* chance to add a WHERE clause to it. The `$targetEntity`
argument is information about which entity we're querying for. Let's dump
that to test that the method is called, and to see what that looks like:

[[[ code('9eb0c2c613') ]]]

## Adding the Filter

Next, Doctrine somehow has to know about this class. If you're using Doctrine
outside of Symfony, you'll use its `Configuration` object and call `addFilter`
on it:

```php
// from http://doctrine-orm.readthedocs.org/en/latest/reference/filters.html#configuration
$config->addFilter('locale', '\Doctrine\Tests\ORM\Functional\MyLocaleFilter');
```

You pass it the class name and some "key" - `locale` in their example.
This becomes its nickname, and we'll refer to the filter later by this key.

In Symfony, we need the same, but it's done with configuration. Open up
`app/config/config.yml` and find the `doctrine` spot, and under `orm`, add
`filters:`. On the next line, go out four spaces, make up a key for the
filter - I'll say `fortune_cookie_discontinued` and set that to the class
name: `AppBundle\Doctrine\DiscontinuedFilter`:

[[[ code('bd1c91b1b4') ]]]

Awesome - now Doctrine knows about our filter.

## Enabling a Filter

But if you refresh the homepage, nothing! We do *not* hit our `die` statement.
Ok, so adding a filter to Doctrine is 2 steps. First, you say "Hey Doctrine,
this filter exists!" We just did that. Second, you need to *enable* the filter.
That ends up being nice, because it means you can enable or disable a filter
on different parts of your site.

Open up `FortuneController`. Let's enable the filter on our homepage. Yes
yes, we *are* going to enable this filter globally for the site later. Just
stay tuned.

To enable it here, first, get the `EntityManager`. And I'm going to add a
comment, which will help with auto-completion on the next steps:

[[[ code('f86b84ca04') ]]]

Once you have the entity manager, call `getFilters()` on it, then `enable()`.
The argument to `enable()` needs to be whatever nickname you gave the filter
before. Actually, I have a typo in mine - I'll fix that now. Copy the
`fortune_cookie_discontinued` string and pass it to `enable()`:

[[[ code('41148f2439') ]]]

Filter class, check! Filter register, check! Filter enabled, check. Moment
of truth. Refresh! And there's our dumped `ClassMetadata`.

## Adding the Filter Logic

We haven't put anything in `DiscontinuedFilter` yet, but most of the work
is done. That `ClassMetadata` argument is your best friend: this is the Doctrine
object that knows *everything* about the entity we're querying for. You can
read your annotation mapping config, get details on associations, find out
about the primary key and anything else your heart desires.

Now, this method will be called for *every* query. But we *only* want to
add our filtering logic if the query is for a `FortuneCookie`. To do that,
add: `if`, `$targetEntity->getReflectionClass()` - that's the PHP `ReflectionClass`
object, `->name() != AppBundle\Entity\FortuneCookie`, then we're going to
return an empty string:

[[[ code('f2619c4809') ]]]

It's *gotta* be an empty string. That tells Doctrine: hey, I don't want to
add any WHERE clauses here - so just leave it alone. If you return null,
it adds the WHERE but doesn't put anything in it.

Below this, it's our time to shine. We're going to return what you want in
the WHERE clause. So we'll use `sprintf`, then `%s`. This will be the table
alias - I'll show you in a second. Then, `.discontinued = false`. This is
the string part of what we normally put in an `andWhere()` with the query
builder. To fill in the `%s`, pass in `$targetTableAlias`:

[[[ code('c83c7c29f7') ]]]

Remember how every entity in a query has an alias? We usually call `createQueryBuilder()`
and pass it something like `fc`. That's the alias. In this case, Doctrine
is *telling* us what the alias is so we can use it.

Alright. Refresh! Um ok, no errors. But it's also not obvious if this is
working. So look at the number of fortune cookies in each category: 1, 2,
3, 3, 3, 4. Go back to `FortuneController` and delete the `enable()` call.
Refresh again. Ah hah! All the numbers went *up* a little. Our filter is
working.

Put the `enable()` call back and refresh again. Click the database icon on
the web debug toolbar. You can see in the query that when we `LEFT JOIN`
to `fortune_cookie`, it added this `f1_.discontinued = false`. 

Woh woh woh. This is more amazing than I've been promising. Even though our
query is for `Category`'s, it was smart enough to apply the filter when it
joined over to `FortuneCookie`. Because of this, when we call `Category::getFortuneCookies()`,
that's *only* going to have the ones that are *not* discontinued.
The filter is applied if the fortune cookie shows up *anywhere* in our query.

## Passing Values to/Configuring a Filter

Sometimes, like in an admin area, we might want to show only *discontinued*
fortune cookies. So can we control the value we're passing in the filter?
To do this, remove `false` and add another `%s`. Add another argument to
`sprintf`: `$this->getParameter('discontinued')`:

[[[ code('71eba395c1') ]]]

This is kind of like the parameters we use in the query builder, except instead
of using `:discontinued`, we concatenate it into the string. But wait! Won't
this make SQL injection attacks possible! I hope you were yelling that :).
But with filters, it's ok because `getParameter()` automatically adds the
escaping. So, it's no worry.

If we *just* did this and refreshed, we've got a great error!

    Parameter 'discontinued' does not exist.

This new approach means that when we enable the filter, we need to pass this
value to it. In `FortuneController`, the `enable()` method actually
returns an instance of our `DiscontinuedFilter`. And now we can call `setParameter()`,
with the parameter name as the first argument and the value we want to set
it to as the second:

[[[ code('9b5cb2f886') ]]]

Refresh! We see the slightly-lower cookie numbers. Change that to `true`
and we should see *really* low numbers. We do!

## Enabling a Filter Globally

Through all of this, you might be asking: "What good is a filter if I need
to enable it all the time." Well first, the nice thing about filters is that
you *do* have this ability to enable or disable them if you need to.

To enable a filter globally, you just need to follow these same steps in
the bootstrap of your app. To hook into the beginning process of Symfony,
we'll need an event listener.

I did the hard-work already and created a class called `BeforeRequestListener`:

[[[ code('8bc2168596') ]]]

For Symfony peeps, you'll recognize the code in my `services.yml`:

[[[ code('9dd9c6d1b0') ]]]

It registers this as a service and the `tags` at the bottom says, "Hey, when
Symfony boots, like right at the very beginning, call the `onKernelRequest`
method." I'm also passing the `EntityManager` as the first argument to the
`__construct()` function. Because, ya know, we need that to enable filters.

Let's go steal the enabling code from `FortuneController`, take it all out
and paste it into `onKernelRequest`. Instead of simply `$em`, we have `$this->em`,
since it's set on a property:

[[[ code('6602c00eaf') ]]]

Let's try it! Even though we took the `enable()` code out of the controller,
the numbers don't change: our filter is still working. If we click into
"Proverbs", we see only 1. But if I disable the filter, we see all 3.

That's it! You're dangerous. If you've ever built a multi-tenant site where
almost *every* query has a filter, life just got easy.
