<?php
/**
 * File: default-nav.php
 * Project: GamerHelpDesk
 * Created Date: March 2026
 * Author: danutz0501 (M. Dumitru Daniel)
 * -----
 * Last Modified:
 * Modified By:
 * -----
 * Copyright (c) 2026 M. Dumitru Daniel (M. Dumitru Daniel)
 *  This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="internal">
            <img src="image/website/logo.svg" alt="GamerHelpDesk Logo" width="35" height="auto" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="services">
                        <svg class="bi" role="img" width="1.5rem" height="1.5rem" fill="blue">
                            <use xlink:href="bootstrap-icons.svg#twitch"/>
                        </svg>
                    &nbsp;Services</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-house-gear"></i>&nbsp;Tools
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="image-editor"><i class="bi bi-images"></i>&nbsp;Image Editor</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="back-up-media"><i class="bi bi-hdd-rack"></i>&nbsp;Back up media</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="back-up-database"><i class="bi bi-database"></i>&nbsp;Back up database</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="download-zip-files"><i class="bi bi-file-earmark-zip"></i>&nbsp;Download zip files</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="add-service"><i class="bi bi-terminal-plus"></i>&nbsp;Add service</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="remove-service"><i class="bi bi-terminal-dash"></i>&nbsp;Remove service</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="service-settings"><i class="bi bi-gear"></i>&nbsp;Service settings</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-twitch"></i>&nbsp;Stream
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="stream-starting"><i class="bi bi-play-circle"></i>&nbsp;Stream starting</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="stream-ending"><i class="bi bi-stop-circle"></i>&nbsp;Stream ending</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="stream-brb"><i class="bi bi-pause-circle"></i>&nbsp;Stream brb</a> 
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="upload-video"><i class="bi bi-camera-video"></i>&nbsp;Upload video</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="upload-image"><i class="bi bi-image"></i>&nbsp;Upload image</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="upload-audio"><i class="bi bi-music-note"></i>&nbsp;Upload audio</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="stream-settings"><i class="bi bi-gear"></i>&nbsp;Stream settings</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="create-slideshow"><i class="bi bi-film"></i>&nbsp;Create slideshow</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notes"><i class="bi bi-sticky"></i>&nbsp;Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="speed-dial"><i class="bi bi-list"></i>&nbsp;Speed dial</a>
                </li>
            </ul>
        </div>        
    </div>
</nav>