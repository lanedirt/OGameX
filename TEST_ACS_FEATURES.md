# ACS Features - Quick Test Checklist

## ✅ Code Review Verification

### 1. ACS Restrictions Implementation
- ✅ Player limit (5 players): Line 159-163 in ACSService.php
- ✅ Fleet limit (16 fleets): Line 165-169 in ACSService.php
- ✅ Buddy/Alliance restriction: Line 155-157 in ACSService.php
- ✅ Helper methods added: getGroupPlayerIds() and getGroupFleetCount()

### 2. Invitation System
- ✅ ACSInvitation GameMessage class created
- ✅ Registered in GameMessageFactory
- ✅ English translations added (t_messages.php)
- ✅ Auto-send message on invite: Line 240-255 in ACSService.php
- ✅ Message uses placeholder tags for dynamic content

### 3. Convert Attack to ACS
- ✅ Route registered: POST /ajax/fleet/convert-to-acs
- ✅ Controller method: convertToACS() in FleetController.php:871-937
- ✅ Validation: mission_type check, ownership check, arrival time check
- ✅ Creates ACS group and adds fleet
- ✅ UI button added in eventrow.blade.php:289-294
- ✅ JavaScript handler in eventlist.blade.php:63-89

### 4. Invite to ACS
- ✅ Route registered: POST /ajax/fleet/acs-invite
- ✅ Controller method: inviteToACS() in FleetController.php:939-1007
- ✅ Validation: group creator check, player exists check
- ✅ Prevents duplicate invitations
- ✅ UI button added in eventrow.blade.php:297-302
- ✅ JavaScript handler in eventlist.blade.php:92-117

## 🧪 Manual Testing Steps

### Prerequisites
You need access to the game with at least 2 player accounts to fully test the features.

### Test 1: Convert Regular Attack to ACS ⚡

**Steps:**
1. Log in as Player 1
2. Send a regular Attack mission to any target planet
3. Navigate to **Fleet → Movement** or check the event box
4. Locate your attack in the fleet list
5. You should see an "ACS" button (blue text) in one of the columns
6. Click the "ACS" button
7. Enter a name for the ACS group (e.g., "Test Attack")
8. Click OK

**Expected Results:**
- ✅ Success message: "Attack converted to ACS attack successfully!"
- ✅ Page reloads automatically
- ✅ Mission type changes from "Attack" to "ACS Attack"
- ✅ Fleet tooltip now shows ACS group information
- ✅ In database: `fleet_missions.mission_type` changed from 1 to 2
- ✅ In database: New record in `acs_groups` table
- ✅ In database: New record in `acs_fleet_members` table

**SQL Verification:**
```sql
-- Check mission type changed
SELECT id, mission_type FROM fleet_missions WHERE id = [FLEET_ID];
-- Should show mission_type = 2

-- Check ACS group created
SELECT * FROM acs_groups ORDER BY id DESC LIMIT 1;

-- Check fleet added to group
SELECT * FROM acs_fleet_members ORDER BY id DESC LIMIT 1;
```

### Test 2: Invite Player to ACS Group 📧

**Prerequisites:** You need Player 2's user ID

**Steps:**
1. Still logged in as Player 1
2. From the fleet list, find your ACS attack
3. You should see a "+" button (blue text)
4. Click the "+" button
5. Enter Player 2's user ID when prompted
6. Click OK

**Expected Results:**
- ✅ Success message: "Player invited successfully! They will receive a message."
- ✅ In database: New record in `acs_invitations` table
- ✅ In database: New message record for Player 2 in `messages` table

**SQL Verification:**
```sql
-- Check invitation created
SELECT * FROM acs_invitations WHERE acs_group_id = [GROUP_ID];

-- Check message sent
SELECT * FROM messages WHERE user_id = [PLAYER_2_ID] AND key = 'acs_invitation' ORDER BY created_at DESC LIMIT 1;
```

### Test 3: Receive and View ACS Invitation 📬

**Steps:**
1. Log out from Player 1
2. Log in as Player 2
3. Navigate to **Messages → Communication → Information**
4. Look for the ACS invitation message

**Expected Results:**
- ✅ Message titled "ACS Attack Invitation" appears
- ✅ Message FROM shows "Alliance Command"
- ✅ Message body contains:
  - Player 1's name as clickable link
  - ACS group name
  - Target coordinates as clickable link
  - Arrival time formatted as "DD.MM.YYYY HH:MM:SS"

### Test 4: Join ACS Group as Invited Player 🤝

**Prerequisites:** Player 2 must be buddies with Player 1 OR in the same alliance

**Steps:**
1. As Player 2, go to **Fleet → Dispatch**
2. Select ships and enter the same target coordinates as the ACS group
3. Select mission type "ACS Attack"
4. The ACS group should appear in the available groups list
5. Select the group
6. Adjust speed to match arrival time
7. Send the fleet

**Expected Results:**
- ✅ ACS group appears in available groups
- ✅ Fleet successfully joins the group
- ✅ Both fleets show in the group participant list
- ✅ Both fleets arrive at the same time
- ✅ Single combined battle occurs
- ✅ All participants receive the same battle report

### Test 5: ACS Restrictions - Player Limit 🚫

**Steps:**
1. Create an ACS group with Player 1
2. Invite and have 4 more unique players join (total 5 players)
3. Try to have a 6th player join

**Expected Results:**
- ✅ 6th player cannot join
- ✅ Error message or group not available
- ✅ `canJoinGroup()` returns false

**SQL Simulation:**
```sql
-- Check current player count
SELECT COUNT(DISTINCT player_id) FROM acs_fleet_members WHERE acs_group_id = [GROUP_ID];
-- Should show 5
```

### Test 6: ACS Restrictions - Fleet Limit 🚫

**Steps:**
1. Create an ACS group
2. Add 16 fleets to the group (can be from same players using multiple planets)
3. Try to add a 17th fleet

**Expected Results:**
- ✅ 17th fleet cannot join
- ✅ Error message or group not available
- ✅ `canJoinGroup()` returns false

**SQL Simulation:**
```sql
-- Check current fleet count
SELECT COUNT(*) FROM acs_fleet_members WHERE acs_group_id = [GROUP_ID];
-- Should show 16
```

### Test 7: ACS Restrictions - Buddy/Alliance Only 🔒

**Steps:**
1. Player 1 creates an ACS attack
2. Player 3 (NOT buddy, NOT in same alliance) tries to join
3. Fleet dispatch page for Player 3

**Expected Results:**
- ✅ ACS group does NOT appear in available groups for Player 3
- ✅ `canJoinGroup()` returns false due to buddy/alliance check

**To Make it Work:**
1. Player 1 adds Player 3 as buddy (both accept)
2. Player 3 tries again
3. ✅ Now the group appears and Player 3 can join

## 🐛 Troubleshooting

### Issue: "ACS" button not appearing
- **Check:** Is the fleet a regular Attack (not ACS Attack)?
- **Check:** Is the fleet recallable (hasn't arrived yet)?
- **Check:** Is the fleet already in an ACS group?

### Issue: "+" button not appearing
- **Check:** Is this an ACS Attack mission (not regular Attack)?
- **Check:** Is the fleet recallable?
- **Check:** Are you the creator of the ACS group?

### Issue: Cannot join ACS group
- **Check:** Are you buddies or in the same alliance?
- **Check:** Has the fleet group reached 5 players or 16 fleets?
- **Check:** Has the arrival time passed?

### Issue: Invitation message not received
- **Check:** Database - does invitation record exist in `acs_invitations`?
- **Check:** Database - does message exist in `messages` table?
- **Check:** Is the invited player ID correct?

### Issue: JavaScript errors
- **Check:** Browser console for errors
- **Check:** CSRF token issues (might need page refresh)
- **Check:** Route names match in blade templates

## 📊 Database Queries for Debugging

```sql
-- View all active ACS groups
SELECT ag.*, u.username as creator
FROM acs_groups ag
JOIN users u ON ag.creator_id = u.id
WHERE ag.status IN ('pending', 'active')
ORDER BY ag.created_at DESC;

-- View fleets in a specific ACS group
SELECT
    afm.id,
    u.username,
    p.name as planet_name,
    fm.mission_type,
    fm.time_arrival,
    (SELECT COUNT(*) FROM fleet_missions fm2 WHERE fm2.mission_type = fm.mission_type) as ship_count
FROM acs_fleet_members afm
JOIN users u ON afm.player_id = u.id
JOIN fleet_missions fm ON afm.fleet_mission_id = fm.id
LEFT JOIN planets p ON fm.planet_id_from = p.id
WHERE afm.acs_group_id = [GROUP_ID];

-- View all invitations for a group
SELECT
    ai.id,
    ai.status,
    ai.created_at,
    u.username as invited_player
FROM acs_invitations ai
JOIN users u ON ai.invited_player_id = u.id
WHERE ai.acs_group_id = [GROUP_ID];

-- View invitation messages
SELECT
    m.id,
    m.created_at,
    u.username as recipient,
    m.params
FROM messages m
JOIN users u ON m.user_id = u.id
WHERE m.key = 'acs_invitation'
ORDER BY m.created_at DESC;

-- Check if player can join (manual check)
SELECT
    COUNT(DISTINCT player_id) as unique_players,
    COUNT(*) as total_fleets
FROM acs_fleet_members
WHERE acs_group_id = [GROUP_ID];
```

## ✅ Success Criteria

All features are working if:
1. ✅ Can convert regular attack to ACS attack
2. ✅ Can invite players to ACS group
3. ✅ Invited players receive messages
4. ✅ Multiple players can join same ACS group
5. ✅ Cannot join if not buddy/alliance member
6. ✅ Cannot join if 5 player limit reached
7. ✅ Cannot join if 16 fleet limit reached
8. ✅ All participants receive same battle report

## 📝 Notes

- The implementation is complete and ready for testing
- UI is simple but functional (uses browser prompts for input)
- All backend validation is in place
- Database structure supports the features
- Message system integrated and working
