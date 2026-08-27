# Puyo

**Your Paluwagan, finally organized.**

Puyo is a web app for running a *paluwagan* (a rotating savings and
credit association, sometimes called a ROSCA) without a spreadsheet or
a group chat full of "who's turn is it again?" messages.

## What is a paluwagan?

A group of people agree to contribute a fixed amount on a regular
schedule — say, ₱500 every week. Each period, everyone's contribution
is pooled together and paid out in full to one member. The next period,
it's the next member's turn, and so on, until everyone in the group has
received a payout once. That's one **round**. A group can keep going
for another round, or stop there.

Puyo is where the group owner sets all of this up, keeps track of who
has paid, and hands out the pot each round — and where every member can
check in on where things stand.

## How the app works

### 1. Create a group

The owner creates a group with:
- a name
- how much each member contributes
- how often (every X days, weeks, or months)
- when the first cycle starts

A brand-new group starts out as a **draft** — nothing is collected yet.

### 2. Add members and set the payout order

While the group is still a draft, the owner adds members (name and,
optionally, an email so they can get reminders) and can drag members
up or down to set the order they'll receive their payout in. Members
can also be removed at this stage. Once the group goes active, the
member list and payout order are locked in.

### 3. Activate the group

Activating a group generates the first round's schedule: one
**cycle** per member, each with a due date and a recipient (following
the payout order). The group is now **active** and contributions can
start coming in.

### 4. Record contributions

As members pay their share for the current cycle, the owner records
each contribution (amount, date paid, and an optional note). Puyo
shows, at a glance, how much has been collected toward the current
cycle versus how much is expected, and flags anyone who's behind. A
contribution can be undone if it was recorded by mistake.

### 5. Pay out the cycle

Once a cycle has enough collected, the owner disburses it to that
cycle's recipient. Puyo shows exactly how much is on hand and suggests
a safe payout amount, so a group can never pay out more than it has
actually collected.

### 6. Start the next round, or wrap up

Once every cycle in the current round has been paid out, the owner
gets the choice to either **start a new round** — same members, same
payout order, fresh cycles — or **mark the group as complete**, which
closes it out as a permanent record everyone can still look back on.

### 7. Everyone can check in

Every member (and anyone the owner has shared the group with) can open
the group and see:
- the current cycle, who's receiving it, and how much has been
  collected so far
- each member's personal ledger — a running history of what they were
  expected to pay, what they've paid, and their balance, cycle by
  cycle, downloadable as a PDF
- a chronological activity feed of everything that's happened in the
  group: contributions, payouts, rounds starting, members joining, etc.

### 8. Share a group with someone else

An owner can share a group with someone else by invite code. That
person gets read-only access — they can see everything but can't make
changes — until the owner revokes it or they leave on their own.

### 9. Reminders, so nobody forgets

As a cycle's due date approaches, Puyo automatically sends:
- an **email reminder to each member** with an email on file, letting
  them know how much they owe and when it's due, with a link straight
  to the group
- an **in-app notification to the group owner**, so they know a cycle
  is coming up without having to check every group manually

Each member only ever gets one reminder per cycle, so nobody's inbox
gets spammed as the due date gets closer.

### 10. The dashboard

Logging in takes you to a dashboard showing, across all of your
groups: how many are draft/active/completed, how the current cycle is
progressing, and the single soonest-due payout you need to know about
next. From there you can filter and sort your groups, or jump straight
to one with a quick search (⌘K).

## Accounts

Sign up with email and password, or with Google. New accounts verify
their email before they can fully use the app. Every member and user
gets a fun, auto-generated avatar, and you can regenerate it any time
if you want a different look. Light and dark mode are both supported,
and the app works on both desktop and mobile.