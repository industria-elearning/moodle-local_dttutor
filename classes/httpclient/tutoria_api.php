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
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tutoria_api {
    /** @var ai_services_api AI services API client instance. */
    private ai_services_api $aiservice;

    /** @var cache|null Cache instance for storing sessions. */
    private ?cache $cache;

    /**
     * Constructor to initialize the Tutor-IA API client.
     *
     * @since Moodle 4.5
     */
    public function __construct() {
        $this->aiservice = new ai_services_api();

        try {
            $this->cache = cache::make('local_dttutor', 'sessions');
        } catch (\Exception $e) {
            debugging('Cache initialization failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $this->cache = null;
        }
    }

    /**
     * Start a new chat session or retrieve an existing one from cache.
     *
     * @param int $courseid Course ID.
     * @return array Session data including session_id, ready status, and TTL.
     * @throws moodle_exception If the session creation fails.
     * @since Moodle 4.5
     */
    public function start_session(int $courseid): array {
        $cachekey = "session_{$courseid}";
        $cached = null;

        if ($this->cache !== null) {
            $cached = $this->cache->get($cachekey);
        }

        if ($cached && $this->is_session_valid($cached)) {
            return $cached;
        }

        $requestdata = ['course_id' => (string)$courseid];

        $response = $this->aiservice->request('POST', '/chat/start', $requestdata);

        $response['created_at'] = time();

        if ($this->cache !== null) {
            $ttl = ($response['session_ttl_seconds'] ?? 604800) - 3600;
            $this->cache->set($cachekey, $response);
        }

        return $response;
    }

    /**
     * Send a message to an existing chat session.
     *
     * @param string $sessionid Session ID.
     * @param string $content Message content.
     * @param array $meta Optional metadata (includes cmid if in module context).
     * @return array Response indicating if message was enqueued.
     * @throws moodle_exception If sending fails.
     * @since Moodle 4.5
     */
    public function send_message(string $sessionid, string $content, array $meta = []): array {
        global $USER;
        return $this->aiservice->request('POST', '/chat/message', [
            'session_id' => $sessionid,
            'content' => $content,
            'meta' => $meta,
            'user_id' => $USER->id,
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
        return $this->aiservice->get_streaming_url_for_session($sessionid);
    }

    /**
     * Get chat history for a session.
     *
     * @param string $sessionid Session ID.
     * @param int $limit Maximum number of messages to return (default: 20).
     * @param int $offset Number of messages to skip for pagination (default: 0).
     * @return array Response with messages array and pagination info.
     * @throws moodle_exception If the request fails.
     * @since Moodle 4.5
     */
    public function get_history(string $sessionid, int $limit = 20, int $offset = 0): array {
        $endpoint = '/chat/history?session_id=' . urlencode($sessionid) .
                    '&limit=' . $limit .
                    '&offset=' . $offset;
        return $this->aiservice->request('GET', $endpoint);
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
        return $this->aiservice->request('DELETE', '/chat/session/' . $sessionid);
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
