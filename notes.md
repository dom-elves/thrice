So I had a really rough idea for how everything outside of the game itself would work, but I'm struggling to settle on how everything outside of it should work, so I'm word dumping a bunch of notes. A PR is not the best place for this, though I like writing in vsc and obsidian sync has been really buggy with my macbook, and I've been doing more research/thought based stuff on there recently.

### Considerations
Here, I'm going to write considerations that I have undecided on, as I find them. This is so I can come to a conclusion of how to proceed, and which things make sense to include/not include.

#### Auth
Everything around this feels fine at present. Just using Laravel Fortify but I need to finish wiring up some extras beyond login and pw reset. Will add any points here if necessary.

#### Lobby privacy
Currently, lobbies can be created by anyone and are accessible by anyone, provided you are sent the link. Password protection is there, just needs to be wired up. Lobbies are destroyed if they turn into a game. Current options:

- Keep as-is
- Remove altogether, and just make it so games can be created directly
- Make lobbies 'discoverable', e.g. a list of active games on the dash
    - If this option is chosen, we have to consider make lobbies private (unlisted or listed & password protected)
    - Would have to consider showing active games, but not lobbies, though that removes the 'discovery' element. E.g. if there are 5 active users all not in a lobby, they have no way of knowing
        - Although, could implement a sort of 'queue', where you can join a game and it just throws you into an active, but unstarted lobby. However, this could possibly require lobbies be persisted to mysql db, I sort of want to avoid that for the time being

#### UX
One issue I ran into was how it felt as a user to create a lobby that turned into a game. Flow:

- Create lobby button
- Enter details, create button
- Redirected to lobby (not jarring, however), await joiners
- Ready is toggleable button
- Game autostarts at 2+ readies
- User is redirected again (more noticable, UI change)
- Untoggleable ready button click
- Game starts at all ready

It just felt like too much clicking? Or maybe that's because I was always acting as two users in order to get the game going. Though it should be more actions if you're the one creating the lobby/game, rather than the joiner.

#### Lobby & Game start logic

I hit a bit of a mental block with the backend logic post-lobby. The creation, processing and destruction of that was fine, it just felt a bit off to essentially use an almost identical process for game creation etc, and then just slap "oh, and start the game" on at the end of the process. Didn't really sit well with me. 


### Processes

#### Login (basically all done, just need to wire up fortify for the remainder)
- ~~Login~~
- ~~Reset Password~~
- Sign up
- Email verification
- Password Confirmation
- Change password

#### Lobbies
Lobbies are *not* stored in the mysql db. They are designed to be always coming up/down at scale. Data is managed by redis sets. They are currently design to be semi-public (password field is there, but not enforced in any way. This is a consideration to decide)

*Create*
- ~~With name~~
- ~~With password~~
- ~~With defaults (neither name or password)~~
- ~~Create a redis set that holds `user_ids`~~
- ~~Basic `Game` hash is created in redis when the lobby is, though mysql model is not~~
- Lobbies have a ttl of 10mins (optional, depends if I do a coutndown for game start)


Join
- ~~Directly via link~~
- Via a menu (may not actually implement)

Leave
- ~~Via button~~
- Via closing tab
- Via closing window
- ~~Last person to leave automatically tears down the lobby, this is done by redis~~

#### Future thoughts
- Lobbies/games to have a vote-kick system.
- Games to have an "owner" (whoever created it), though this will remove the poker table feel of the game.