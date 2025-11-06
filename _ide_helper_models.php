<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string|null $user_id
 * @property int $action_type_id
 * @property string $loggable_type
 * @property int $loggable_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $loggable
 * @property-read \Modules\Core\Models\User|null $users
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereActionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereLoggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereLoggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $commentable_id
 * @property string $commentable_type
 * @property string $content
 * @property string $user_id
 * @property bool $is_flagged
 * @property string|null $flagged_by
 * @property int|null $flagged_at
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \Modules\Core\Models\User|null $flagger
 * @property-read Comment|null $parent
 * @property-read \Modules\Core\Models\User|null $poster
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereFlaggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereFlaggedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 */
	class Comment extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $user_id
 * @property int $type_id
 * @property string $value
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Modules\Core\Models\Type $type
 * @property-read \Modules\Core\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact byUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereValue($value)
 */
	class Contact extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $favoriteable_id
 * @property string $favoriteable_type
 * @property string $collection
 * @property string $user_id
 * @property int $favorites_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $favoriteable
 * @property-read \Modules\Core\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereFavoriteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereFavoriteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereFavoritesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $imageable_id
 * @property string $imageable_type
 * @property string $disk
 * @property string $image_path
 * @property string $image_url
 * @property string $image_mime
 * @property string|null $image_alt
 * @property string $type
 * @property bool $is_flagged
 * @property string|null $flagged_by
 * @property \Illuminate\Support\Carbon|null $flagged_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Modules\Core\Models\User|null $flagger
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $imageable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereFlaggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereFlaggedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 */
	class Image extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role usersByRole(array|string $roles)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $type
 * @property string|null $description
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Modules\Core\Models\User|null $creator
 * @property-read \Modules\Core\Models\User|null $deletor
 * @property-read \Modules\Core\Models\User|null $updator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withoutTrashed()
 */
	class Setting extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $context
 * @property string $text_color
 * @property string $bg_color
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereUpdatedAt($value)
 */
	class Status extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $context
 * @property string $text_color
 * @property string $bg_color
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereUpdatedAt($value)
 */
	class Type extends \Eloquent {}
}

namespace Modules\Core\Models{
/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $slug_name
 * @property string $email
 * @property string $password
 * @property int|null $status_id
 * @property int $token_version
 * @property string|null $otp
 * @property \Illuminate\Support\Carbon|null $otp_expires_at
 * @property string|null $two_factor_secret
 * @property array<array-key, mixed>|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $last_visited_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read mixed $fullname
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\ActivityLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Modules\Core\Models\Status|null $status
 * @property-read mixed $status_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole(array|string $roles)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byStatus(array|string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User has2FA()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastVisitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOtpExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSlugName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTokenVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withContacts(bool $active = true, ?int $typeId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withStatus()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \Filament\Models\Contracts\HasName, \Tymon\JWTAuth\Contracts\JWTSubject {}
}

namespace Modules\Core\Models{
/**
 * @property int $id
 * @property string $user_id
 * @property string $device_name
 * @property string|null $device_token
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Core\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice active(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice inactive(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUserId($value)
 */
	class UserDevice extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property int $year_number
 * @property string $group_code
 * @property string $display_name
 * @property int|null $major_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Domain\Models\Major|null $major
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel byGroup(string $code)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel byMajor(string $major)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel byYear(int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereGroupCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereMajorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicLevel whereYearNumber($value)
 */
	class AcademicLevel extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property string $id
 * @property string $teacher_id
 * @property int $subject_id
 * @property \Illuminate\Support\Carbon $start_at
 * @property \Illuminate\Support\Carbon $end_at
 * @property float $lat
 * @property float $lng
 * @property int $radius
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $duration
 * @property-read mixed $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Modules\Domain\Models\Subject $subject
 * @property-read mixed $subject_name
 * @property-read \Modules\Domain\Models\Teacher $teacher
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom attended()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom notAttended()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom past()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom upcomming()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereUpdatedBy($value)
 */
	class Classroom extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $iso_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Governorate whereUpdatedAt($value)
 */
	class Governorate extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereUpdatedAt($value)
 */
	class Major extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $user_id
 * @property string $payable_id
 * @property string $payable_type
 * @property string $paypal_transaction_id
 * @property int $amount_cents
 * @property string $currency
 * @property array<array-key, mixed>|null $product_data
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Core\Models\User|null $buyer
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payable
 * @property-read \Modules\Core\Models\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereAmountCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment wherePaypalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereProductData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PayPalPayment whereUserId($value)
 */
	class PayPalPayment extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $buyer_id
 * @property string $payable_id
 * @property string $payable_type
 * @property string|null $stripe_payment_intent_id
 * @property string $stripe_transaction_id
 * @property int $amount_cents
 * @property string $currency
 * @property array<array-key, mixed>|null $product_data
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Core\Models\User $buyer
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payable
 * @property-read \Modules\Core\Models\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereAmountCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereProductData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereStripeTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StripePayment whereUpdatedAt($value)
 */
	class StripePayment extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $student_code
 * @property string $hashed_national_id
 * @property string $gender
 * @property int|null $academic_level_id
 * @property int $warning_count
 * @property bool $is_banned
 * @property string|null $address
 * @property string|null $city
 * @property int|null $governorate_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Classroom> $classrooms
 * @property-read int|null $classrooms_count
 * @property-read \Modules\Domain\Models\Governorate|null $governorate
 * @property-read \Modules\Core\Models\User $user
 * @property-read mixed $username
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student byCity(string $city)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student byGender(string $gender)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student byGovernorate(string|int $governorateId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAcademicLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGovernorateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereHashedNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWarningCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withoutTrashed()
 */
	final class Student extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereUpdatedAt($value)
 */
	class Subject extends \Eloquent {}
}

namespace Modules\Domain\Models{
/**
 * @property int $id
 * @property string $user_id
 * @property string $teacher_code
 * @property int $teacher_type_id
 * @property int $status_id
 * @property string|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Modules\Core\Models\User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Domain\Models\Department> $departments
 * @property-read int|null $departments_count
 * @property-read \Modules\Core\Models\Status $status
 * @property-read \Modules\Core\Models\Type $type
 * @property-read \Modules\Core\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereTeacherCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereTeacherTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUserId($value)
 */
	class Teacher extends \Eloquent {}
}

