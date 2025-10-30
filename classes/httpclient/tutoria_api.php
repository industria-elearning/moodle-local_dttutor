<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tutor-IA HTTP client for chat API communication
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\httpclient;

use aiprovider_datacurso\httpclient\ai_services_api;
use cache;
use moodle_exception;

/**
 * HTTP client for Tutor-IA Chat API
 *
 * @package    local_dttutor
 * @category   local
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tutoria_api {

    /** @var ai_services_api AI services API client instance. */
    private ai_services_api $ai_service;

    /** @var cache Cache instance for storing sessions. */
    private cache $cache;

    /**
     * Constructor to initialize the Tutor-IA API client.
     *
     * @since Moodle 4.5
     */
    public function __construct() {
        $this->ai_service = new ai_services_api();
        $this->cache = cache::make('local_dttutor', 'sessions');
    }

    /**
     * Start a new chat session or retrieve an existing one from cache.
     *
     * @param int $courseid Course ID.
     * @param int $cmid Course Module ID (0 if only in course).
     * @return array Session data including session_id, ready status, and TTL.
     * @throws moodle_exception If the session creation fails.
     * @since Moodle 4.5
     */
    public function start_session(int $courseid, int $cmid = 0): array {
        // Check cache first. Include cmid in cache key if present.
        $cachekey = ($cmid > 0) ? "session_{$courseid}_{$cmid}" : "session_{$courseid}";
        $cached = $this->cache->get($cachekey);

        if ($cached && $this->is_session_valid($cached)) {
            return $cached;
        }

        // Create new session. Include cmid if present.
        $requestdata = ['course_id' => (string)$courseid];
        if ($cmid > 0) {
            $requestdata['cmid'] = (string)$cmid;
        }

        $response = $this->ai_service->request('POST', '/chat/start', $requestdata);

        // Add creation timestamp for validation.
        $response['created_at'] = time();

        // Store in cache with TTL (session TTL minus 1 hour margin).
        $ttl = ($response['session_ttl_seconds'] ?? 604800) - 3600;
        $this->cache->set($cachekey, $response);

        return $response;
    }

    /**
     * Send a message to an existing chat session.
     *
     * @param string $sessionid Session ID.
     * @param string $content Message content.
     * @param array $meta Optional metadata.
     * @param int|null $cmid Course Module ID (optional).
     * @return array Response indicating if message was enqueued.
     * @throws moodle_exception If sending fails.
     * @since Moodle 4.5
     */
    public function send_message(string $sessionid, string $content, array $meta = [], ?int $cmid = null): array {
        global $USER;
        return $this->ai_service->request('POST','/chat/message', [
            'session_id' => $sessionid,
            'content' => $content,
            'meta' => $meta,
            'user_id' => $USER->id,
            'cmid' => $cmid
        ]);
    }

    /**
     * Build the SSE stream URL for a chat session.
     *
     * @param string $sessionid Session ID.
     * @return string Full stream URL with session parameter.
     * @since Moodle 4.5
     */
    public function get_stream_url(string $sessionid): string {
        return $this->ai_service->get_streaming_url_for_session($sessionid);
    }

    /**
     * Delete a chat session.
     *
     * @param string $sessionid Session ID to delete.
     * @return array Response with deletion status.
     * @throws moodle_exception If deletion fails.
     * @since Moodle 4.5
     */
    public function delete_session(string $sessionid): array {
        return $this->ai_service->request('DELETE', '/chat/session/'.$sessionid);
    }

    /**
     * Check if a cached session is still valid.
     *
     * @param array $session Cached session data.
     * @return bool True if session is still valid.
     */
    private function is_session_valid(array $session): bool {
        if (!isset($session['created_at']) || !isset($session['session_ttl_seconds'])) {
            return false;
        }

        $elapsed = time() - $session['created_at'];
        $ttl = $session['session_ttl_seconds'];

        return $elapsed < ($ttl - 3600); // 1 hour margin.
    }
}
