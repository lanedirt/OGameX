# ACS System Implementation Summary

## Features Implemented

### 1. ACS Group Restrictions ✅
**File:** `app/Services/ACSService.php` (lines 137-191)

**Restrictions:**
- Maximum 5 unique players per ACS group
- Maximum 16 fleets total per ACS group
- Buddy or alliance member requirement still enforced
- Creator of the group can always join (subject to limits)

**New Methods:**
- `getGroupPlayerIds()` - Returns unique player IDs in a group
- `getGroupFleetCount()` - Returns total fleet count in a group

### 2. Invitation System ✅
**Files:**
- `app/GameMessages/ACSInvitation.php` - New message class
- `app/Factories/GameMessageFactory.php` - Registration
- `resources/lang/en/t_messages.php` - Message templates
- `app/Services/ACSService.php:223-258` - Auto-send logic

**Features:**
- Players receive messages under Communication → Information when invited
- Messages include: inviter name, ACS group name, target coordinates, arrival time
- Uses placeholder tags for dynamic content (player names link to profiles, coordinates link to galaxy)

### 3. Convert Regular Attack to ACS Attack ✅
**Backend:**
- **Route:** `POST /ajax/fleet/convert-to-acs`
- **Controller:** `app/Http/Controllers/FleetController.php:871-937`
- **Validation:**
  - Checks fleet mission exists and belongs to player
  - Only regular attacks (mission_type = 1) can be converted
  - Fleet must not have arrived yet
  - Changes mission_type from 1 to 2
  - Creates new ACS group automatically
  - Adds fleet to the group

**Frontend:**
- **UI Location:** Fleet Events (eventbox/eventlist)
- **Button:** "ACS" button appears on regular attack fleets
- **Conditions:** Only shows if mission is a regular attack, not in ACS group, and recallable
- **UX:** Prompts for ACS group name, shows success/error messages

### 4. Invite Players to ACS Groups ✅
**Backend:**
- **Route:** `POST /ajax/fleet/acs-invite`
- **Controller:** `app/Http/Controllers/FleetController.php:939-1007`
- **Validation:**
  - Only ACS group creator can invite
  - Checks player exists
  - Prevents duplicate invitations
  - Sends message automatically

**Frontend:**
- **UI Location:** Fleet Events (eventbox/eventlist)
- **Button:** "+" button appears on ACS attack fleets
- **Conditions:** Only shows if mission is ACS attack and recallable
- **UX:** Prompts for player ID, shows success/error messages

## Testing Instructions

### Test 1: ACS Group Restrictions

#### Test Maximum Players (5 players)
1. Create account player1
2. Player1 sends regular attack, converts to ACS
3. Add players 2, 3, 4, 5 as buddies
4. Each buddy joins the ACS group
5. Try to add 6th buddy → Should fail with "max 5 players" error

#### Test Maximum Fleets (16 fleets)
1. Create ACS group
2. Add 16 fleets (from same or different players)
3. Try to add 17th fleet → Should fail with "max 16 fleets" error

#### Test Buddy/Alliance Restriction
1. Player1 creates ACS attack
2. Player2 (not buddy, not alliance) tries to join → Should fail
3. Add Player2 as buddy
4. Player2 tries to join → Should succeed

### Test 2: Convert Attack to ACS

1. Send a regular attack fleet to another player
2. Go to Fleet → Movement (or check fleet eventbox)
3. Find your attack fleet in the list
4. Click the "ACS" button next to it
5. Enter ACS group name when prompted (e.g., "Joint Strike")
6. Verify success message appears
7. Page reloads, fleet should now show as ACS attack
8. Check fleet tooltip - should show ACS group information

**Expected Results:**
- Mission type changes from "Attack" to "ACS Attack"
- Fleet tooltip shows ACS group name and participant count
- Fleet now appears in available ACS groups for the target

### Test 3: Invite Players to ACS Group

**Prerequisites:** You need another player's ID. You can find this by:
- Looking in the database: `SELECT id, username FROM users WHERE id != YOUR_ID LIMIT 1`
- Or checking a player's profile URL (if implemented)

**Steps:**
1. Create an ACS attack (send attack + convert to ACS, OR join existing ACS)
2. Go to Fleet → Movement
3. Find your ACS attack fleet
4. Click the "+" button
5. Enter target player ID when prompted
6. Verify success message: "Player invited successfully! They will receive a message."

**Verify Invitation:**
1. Log in as the invited player
2. Go to Messages → Communication → Information
3. Should see message: "ACS Attack Invitation"
4. Message should include:
   - Inviter name (clickable link)
   - ACS group name
   - Target coordinates (clickable link to galaxy)
   - Arrival time

### Test 4: Join ACS After Invitation

1. Invited player receives message
2. Player dispatches fleet to same coordinates
3. Selects mission type "ACS Attack"
4. Should see the ACS group in available groups
5. Selects the group and sends fleet
6. Both fleets arrive together
7. Single combined battle report sent to all participants

### Test 5: Restrictions During Join

**Test Player Limit:**
1. Create ACS with 5 different players
2. 6th player tries to join
3. Should see error: Cannot join (player limit reached)

**Test Fleet Limit:**
1. Create ACS with 16 fleets
2. Another player tries to add 17th fleet
3. Should see error: Cannot join (fleet limit reached)

**Test Non-Buddy/Alliance:**
1. Player A creates ACS
2. Player B (not buddy, not alliance) tries to join
3. Should see error or no group available

## Database Verification Queries

```sql
-- Check ACS groups
SELECT * FROM acs_groups WHERE status IN ('pending', 'active');

-- Check fleet members in a group
SELECT afm.*, fm.mission_type, u.username
FROM acs_fleet_members afm
JOIN fleet_missions fm ON afm.fleet_mission_id = fm.id
JOIN users u ON afm.player_id = u.id
WHERE afm.acs_group_id = [GROUP_ID];

-- Check invitations
SELECT ai.*, u.username as invited_player
FROM acs_invitations ai
JOIN users u ON ai.invited_player_id = u.id
WHERE ai.acs_group_id = [GROUP_ID];

-- Check invitation messages
SELECT m.*, u.username
FROM messages m
JOIN users u ON m.user_id = u.id
WHERE m.key = 'acs_invitation'
ORDER BY m.created_at DESC;

-- Count players in ACS group
SELECT COUNT(DISTINCT player_id) as player_count
FROM acs_fleet_members
WHERE acs_group_id = [GROUP_ID];

-- Count fleets in ACS group
SELECT COUNT(*) as fleet_count
FROM acs_fleet_members
WHERE acs_group_id = [GROUP_ID];
```

## Known Limitations

1. **Player ID Input:** Users need to know the player ID to invite. Future enhancement could be a player search by name.
2. **UI Feedback:** Invitation status not shown in real-time (need to reload to see updated participant count)
3. **Group Management:** No dedicated ACS management page yet (could be added later)
4. **Cancel Invitation:** No UI to cancel sent invitations (invitation stays in 'pending' state)

## Files Modified

### Backend
- `app/Services/ACSService.php` - Core ACS logic with restrictions
- `app/Http/Controllers/FleetController.php` - Convert & invite endpoints
- `app/GameMessages/ACSInvitation.php` - New invitation message
- `app/Factories/GameMessageFactory.php` - Message registration
- `resources/lang/en/t_messages.php` - Message templates
- `routes/web.php` - New routes

### Frontend
- `resources/views/ingame/fleetevents/eventrow.blade.php` - UI buttons
- `resources/views/ingame/fleetevents/eventlist.blade.php` - JavaScript handlers

## API Endpoints

### POST /ajax/fleet/convert-to-acs
**Parameters:**
- `fleet_mission_id` - ID of the fleet mission to convert
- `acs_group_name` - Name for the new ACS group (optional, default: "ACS Attack")
- `_token` - CSRF token

**Response:**
```json
{
  "success": true,
  "message": "Attack converted to ACS attack successfully",
  "acs_group_id": 123,
  "acs_group_name": "Joint Strike"
}
```

### POST /ajax/fleet/acs-invite
**Parameters:**
- `acs_group_id` - ID of the ACS group
- `player_id` - ID of the player to invite
- `_token` - CSRF token

**Response:**
```json
{
  "success": true,
  "message": "Player invited successfully"
}
```

## Next Steps / Future Enhancements

1. **Player Search UI:** Instead of entering player ID, search by username
2. **ACS Management Page:** Dedicated page to view/manage your ACS groups
3. **Invitation List:** Show pending invitations in ACS UI
4. **Accept/Decline UI:** Buttons to accept/decline invitations directly from message
5. **Group Chat:** Communication between ACS participants
6. **Formation Settings:** Allow setting attack formation, target priority, etc.
7. **Better Visual Feedback:** Real-time participant counter, fleet arrival timeline
8. **Notification System:** Alert when someone joins/leaves ACS group
