<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Models\AllianceRank;
use OGame\Models\User;

/**
 * Class AllianceService.
 *
 * Alliance service object.
 *
 * @package OGame\Services
 */
class AllianceService
{
    /**
     * The alliance model instance.
     *
     * @var Alliance|null
     */
    private ?Alliance $alliance = null;

    /**
     * AllianceService constructor.
     *
     * @param int $alliance_id
     */
    public function __construct(int $alliance_id = 0)
    {
        if ($alliance_id !== 0) {
            $this->load($alliance_id);
        }
    }

    /**
     * Load alliance by ID.
     *
     * @param int $alliance_id
     * @return void
     */
    public function load(int $alliance_id): void
    {
        $this->alliance = Alliance::with(['founder', 'members.user', 'ranks'])->find($alliance_id);
    }

    /**
     * Get alliance ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->alliance?->id;
    }

    /**
     * Check if alliance exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->alliance !== null;
    }

    /**
     * Get alliance tag.
     *
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->alliance?->tag;
    }

    /**
     * Get alliance name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->alliance?->name;
    }

    /**
     * Get alliance description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->alliance?->description;
    }

    /**
     * Get alliance logo URL.
     *
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->alliance?->logo;
    }

    /**
     * Get alliance external URL.
     *
     * @return string|null
     */
    public function getExternalUrl(): ?string
    {
        return $this->alliance?->external_url;
    }

    /**
     * Get alliance internal text.
     *
     * @return string|null
     */
    public function getInternalText(): ?string
    {
        return $this->alliance?->internal_text;
    }

    /**
     * Get alliance application text.
     *
     * @return string|null
     */
    public function getApplicationText(): ?string
    {
        return $this->alliance?->application_text;
    }

    /**
     * Check if alliance is open for applications.
     *
     * @return bool
     */
    public function isOpenForApplications(): bool
    {
        return $this->alliance?->open_for_applications ?? false;
    }

    /**
     * Get alliance founder.
     *
     * @return User|null
     */
    public function getFounder(): ?User
    {
        return $this->alliance?->founder;
    }

    /**
     * Get all alliance members.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->alliance?->members ?? new Collection();
    }

    /**
     * Get member count.
     *
     * @return int
     */
    public function getMemberCount(): int
    {
        return $this->alliance?->members->count() ?? 0;
    }

    /**
     * Get all alliance ranks.
     *
     * @return Collection
     */
    public function getRanks(): Collection
    {
        return $this->alliance?->ranks()->orderBy('sort_order')->get() ?? new Collection();
    }

    /**
     * Get pending applications.
     *
     * @return Collection
     */
    public function getPendingApplications(): Collection
    {
        return $this->alliance?->pendingApplications()->with('user')->get() ?? new Collection();
    }

    /**
     * Create a new alliance.
     *
     * @param array $data
     * @param User $founder
     * @return Alliance
     * @throws Exception
     */
    public static function createAlliance(array $data, User $founder): Alliance
    {
        // Validate tag uniqueness
        if (Alliance::where('tag', $data['tag'])->exists()) {
            throw new Exception('Alliance tag already exists.');
        }

        // Create alliance
        $alliance = Alliance::create([
            'tag' => $data['tag'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'logo' => $data['logo'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'internal_text' => $data['internal_text'] ?? null,
            'application_text' => $data['application_text'] ?? null,
            'founder_id' => $founder->id,
            'open_for_applications' => $data['open_for_applications'] ?? true,
        ]);

        // Create default ranks
        static::createDefaultRanks($alliance);

        // Add founder as first member with highest rank
        $founderRank = $alliance->ranks()->where('name', 'Leader')->first();
        AllianceMember::create([
            'alliance_id' => $alliance->id,
            'user_id' => $founder->id,
            'rank_id' => $founderRank?->id,
            'joined_at' => Carbon::now(),
        ]);

        return $alliance;
    }

    /**
     * Create default alliance ranks.
     *
     * @param Alliance $alliance
     * @return void
     */
    private static function createDefaultRanks(Alliance $alliance): void
    {
        $defaultRanks = [
            [
                'name' => 'Leader',
                'can_invite' => true,
                'can_kick' => true,
                'can_see_applications' => true,
                'can_accept_applications' => true,
                'can_edit_alliance' => true,
                'can_manage_ranks' => true,
                'can_send_circular_message' => true,
                'can_view_member_list' => true,
                'can_use_alliance_depot' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Officer',
                'can_invite' => true,
                'can_kick' => false,
                'can_see_applications' => true,
                'can_accept_applications' => true,
                'can_edit_alliance' => false,
                'can_manage_ranks' => false,
                'can_send_circular_message' => true,
                'can_view_member_list' => true,
                'can_use_alliance_depot' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Member',
                'can_invite' => false,
                'can_kick' => false,
                'can_see_applications' => false,
                'can_accept_applications' => false,
                'can_edit_alliance' => false,
                'can_manage_ranks' => false,
                'can_send_circular_message' => false,
                'can_view_member_list' => true,
                'can_use_alliance_depot' => false,
                'sort_order' => 2,
            ],
        ];

        foreach ($defaultRanks as $rank) {
            AllianceRank::create(array_merge($rank, ['alliance_id' => $alliance->id]));
        }
    }

    /**
     * Update alliance information.
     *
     * @param array $data
     * @return void
     */
    public function update(array $data): void
    {
        if ($this->alliance) {
            $this->alliance->update($data);
        }
    }

    /**
     * Delete the alliance.
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->alliance) {
            $this->alliance->delete();
        }
    }

    /**
     * Check if a user is a member of the alliance.
     *
     * @param int $user_id
     * @return bool
     */
    public function isMember(int $user_id): bool
    {
        return $this->alliance?->members()->where('user_id', $user_id)->exists() ?? false;
    }

    /**
     * Get member's rank.
     *
     * @param int $user_id
     * @return AllianceRank|null
     */
    public function getMemberRank(int $user_id): ?AllianceRank
    {
        $member = $this->alliance?->members()->where('user_id', $user_id)->first();
        return $member?->rank;
    }

    /**
     * Check if a user has a specific permission.
     *
     * @param int $user_id
     * @param string $permission
     * @return bool
     */
    public function hasPermission(int $user_id, string $permission): bool
    {
        // Founder always has all permissions
        if ($this->alliance && $this->alliance->founder_id === $user_id) {
            return true;
        }

        $rank = $this->getMemberRank($user_id);
        if (!$rank) {
            return false;
        }

        return $rank->{$permission} ?? false;
    }

    /**
     * Search alliances by tag or name.
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        return Alliance::where('tag', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->with('founder')
            ->withCount('members')
            ->get();
    }

    /**
     * Get alliance model instance.
     *
     * @return Alliance|null
     */
    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }
}
