<?php
/**
 * PHP Exif Native Reader Adapter
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Adapter;

use PHPExif\Exif;
use FFMpeg;

/**
 * PHP Exif Native Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class Native extends AdapterAbstract
{
    const INCLUDE_THUMBNAIL = true;
    const NO_THUMBNAIL      = false;

    const SECTIONS_AS_ARRAYS    = true;
    const SECTIONS_FLAT         = false;

    const SECTION_FILE      = 'FILE';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_THUMBNAIL = 'THUMBNAIL';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_IPTC      = 'IPTC';

    /**
     * List of EXIF sections
     *
     * @var array
     */
    protected $requiredSections = array();

    /**
     * Include the thumbnail in the EXIF data?
     *
     * @var boolean
     */
    protected $includeThumbnail = self::NO_THUMBNAIL;

    /**
     * Parse the sections as arrays?
     *
     * @var boolean
     */
    protected $sectionsAsArrays = self::SECTIONS_FLAT;

    /**
     * @var string
     */
    protected $mapperClass = '\\PHPExif\\Mapper\\Native';

    /**
     * Contains the mapping of names to IPTC field numbers
     *
     * @var array
     */
    protected $iptcMapping = array(
        'title'     => '2#005',
        'keywords'  => '2#025',
        'copyright' => '2#116',
        'caption'   => '2#120',
        'headline'  => '2#105',
        'credit'    => '2#110',
        'source'    => '2#115',
        'jobtitle'  => '2#085'
    );


    /**
     * Getter for the EXIF sections
     *
     * @return array
     */
    public function getRequiredSections()
    {
        return $this->requiredSections;
    }

    /**
     * Setter for the EXIF sections
     *
     * @param array $sections List of EXIF sections
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setRequiredSections(array $sections)
    {
        $this->requiredSections = $sections;

        return $this;
    }

    /**
     * Adds an EXIF section to the list
     *
     * @param string $section
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function addRequiredSection($section)
    {
        if (!in_array($section, $this->requiredSections)) {
            array_push($this->requiredSections, $section);
        }

        return $this;
    }

    /**
     * Define if the thumbnail should be included into the EXIF data or not
     *
     * @param boolean $value
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setIncludeThumbnail($value)
    {
        $this->includeThumbnail = $value;

        return $this;
    }

    /**
     * Returns if the thumbnail should be included into the EXIF data or not
     *
     * @return boolean
     */
    public function getIncludeThumbnail()
    {
        return $this->includeThumbnail;
    }

    /**
     * Define if the sections should be parsed as arrays
     *
     * @param boolean $value
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setSectionsAsArrays($value)
    {
        $this->sectionsAsArrays = (bool) $value;

        return $this;
    }

    /**
     * Returns if the sections should be parsed as arrays
     *
     * @return boolean
     */
    public function getSectionsAsArrays()
    {
        return $this->sectionsAsArrays;
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif|boolean Instance of Exif object with data
     */
    public function getExifFromFile($file)
    {
        $mimeType = mime_content_type($file);

        if (strpos($mimeType, 'video') !== 0) {

          // Photo
          $sections   = $this->getRequiredSections();
          $sections   = implode(',', $sections);
          $sections   = (empty($sections)) ? null : $sections;

          $data = @exif_read_data(
              $file,
              $sections,
              $this->getSectionsAsArrays(),
              $this->getIncludeThumbnail()
          );

          if (false === $data) {
              return false;
          }

          $xmpData = $this->getIptcData($file);
          $data = array_merge($data, array(self::SECTION_IPTC => $xmpData));

        } else {
          // Video
          try {

            $data = $this->getVideoData($file);
            $data['MimeType'] = $mimeType;

          } catch (Exception $exception) {
            Logs::error(__METHOD__, __LINE__, $exception->getMessage());
          }
        }

        // map the data:
        $mapper = $this->getMapper();
        $mappedData = $mapper->mapRawData($data);

        // hydrate a new Exif object
        $exif = new Exif();
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($exif, $mappedData);
        $exif->setRawData($data);

        return $exif;
    }

    /**
     * Returns an array of video data
     *
     * @param string $file The file to read the video data from
     * @return array
     */
    public function getVideoData($filename)
    {

      $metadata['FileSize'] = filesize($filename);

      $path_ffmpeg = exec('which ffmpeg');
      $path_ffprobe = exec('which ffprobe');
      $ffprobe = FFMpeg\FFProbe::create(array(
                    'ffmpeg.binaries'  => $path_ffmpeg,
                    'ffprobe.binaries' => $path_ffprobe,
                ));

  		$stream = $ffprobe->streams($filename)->videos()->first()->all();
  		$format = $ffprobe->format($filename)->all();
  		if (isset($stream['width'])) {
  			$metadata['Width'] = $stream['width'];
  		}
  		if (isset($stream['height'])) {
  			$metadata['Height'] = $stream['height'];
  		}
  		if (isset($stream['tags']) && isset($stream['tags']['rotate']) && ($stream['tags']['rotate'] === '90' || $stream['tags']['rotate'] === '270')) {
  			$tmp = $metadata['Width'];
  			$metadata['Width'] = $metadata['Height'];
  			$metadata['Height'] = $tmp;
  		}
  		if (isset($stream['avg_frame_rate'])) {
  			$framerate = explode('/', $stream['avg_frame_rate']);
  			if (count($framerate) == 1) {
  				$framerate = $framerate[0];
  			} elseif (count($framerate) == 2 && $framerate[1] != 0) {
  				$framerate = number_format($framerate[0] / $framerate[1], 3);
  			} else {
  				$framerate = '';
  			}
  			if ($framerate !== '') {
  				$metadata['framerate'] = $framerate;
  			}
  		}
  		if (isset($format['duration'])) {
  			$metadata['duration'] = number_format($format['duration'], 3);
  		}
  		if (isset($format['tags'])) {
  			if (isset($format['tags']['creation_time']) && strtotime($format['tags']['creation_time']) !== 0) {
  				$metadata['DateTimeOriginal'] = date('Y-m-d H:i:s', strtotime($format['tags']['creation_time']));
  			}
  			if (isset($format['tags']['location'])) {
  				$matches = [];
  				preg_match('/^([+-][0-9\.]+)([+-][0-9\.]+)\/$/', $format['tags']['location'], $matches);
  				if (count($matches) == 3 &&
  					!preg_match('/^\+0+\.0+$/', $matches[1]) &&
  					!preg_match('/^\+0+\.0+$/', $matches[2])) {
  					$metadata['GPSLatitude'] = $matches[1];
  					$metadata['GPSLongitude'] = $matches[2];
  				}
  			}
  			// QuickTime File Format defines several additional metadata
  			// Source: https://developer.apple.com/library/archive/documentation/QuickTime/QTFF/Metadata/Metadata.html
  			// Special case: iPhones write into tags->creation_time the creation time of the file
  			// -> When converting the video from HEVC (iOS Video format) to MOV, the creation_time
  			// is the time when the mov file was created, not when the video was shot (fixed in iOS12)
  			// (see e.g. https://michaelkummer.com/tech/apple/photos-videos-wrong-date/ (for the symptom)
  			// Solution: Use com.apple.quicktime.creationdate which is the true creation date of the video
  			if (isset($format['tags']['com.apple.quicktime.creationdate'])) {
  				$metadata['DateTimeOriginal'] = date('Y-m-d H:i:s', strtotime($format['tags']['com.apple.quicktime.creationdate']));
  			}
  			if (isset($format['tags']['com.apple.quicktime.description'])) {
  				$metadata['description'] = $format['tags']['com.apple.quicktime.description'];
  			}
  			if (isset($format['tags']['com.apple.quicktime.title'])) {
  				$metadata['title'] = $format['tags']['com.apple.quicktime.title'];
  			}
  			if (isset($format['tags']['com.apple.quicktime.keywords'])) {
  				$metadata['keywords'] = $format['tags']['com.apple.quicktime.keywords'];
  			}
  			if (isset($format['tags']['com.apple.quicktime.location.ISO6709'])) {
  				$location_data = $this->readISO6709($format['tags']['com.apple.quicktime.location.ISO6709']);
  				$metadata['GPSLatitude'] = $location_data['latitude'];
  				$metadata['GPSLongitude'] = $location_data['longitude'];
  				$metadata['GPSAltitude'] = $location_data['altitude'];
  			}
  			// Not documented, but available on iPhone videos
  			if (isset($format['tags']['com.apple.quicktime.make'])) {
  				$metadata['Make'] = $format['tags']['com.apple.quicktime.make'];
  			}
  			// Not documented, but available on iPhone videos
  			if (isset($format['tags']['com.apple.quicktime.model'])) {
  				$metadata['Model'] = $format['tags']['com.apple.quicktime.model'];
  			}
  		}

      return $metadata;
    }

    /**
  	 * Converts results of ISO6709 parsing
  	 * to decimal format for latitude and longitude
  	 * See https://github.com/seanson/python-iso6709.git.
  	 *
  	 * @param string sign
  	 * @param string degrees
  	 * @param string minutes
  	 * @param string seconds
  	 * @param string fraction
  	 *
  	 * @return float
  	 */
  	private function convertDMStoDecimal(string $sign, string $degrees, string $minutes, string $seconds, string $fraction): float
  	{
  		if ($fraction !== '') {
  			if ($seconds !== '') {
  				$seconds = $seconds . $fraction;
  			} elseif ($minutes !== '') {
  				$minutes = $minutes . $fraction;
  			} else {
  				$degrees = $degrees . $fraction;
  			}
  		}
  		$decimal = floatval($degrees) + floatval($minutes) / 60.0 + floatval($seconds) / 3600.0;
  		if ($sign == '-') {
  			$decimal = -1.0 * $decimal;
  		}
  		return $decimal;
  	}

    /**
  	 * Returns the latitude, longitude and altitude
  	 * of a GPS coordiante formattet with ISO6709
  	 * See https://github.com/seanson/python-iso6709.git.
  	 *
  	 * @param string val_ISO6709
  	 *
  	 * @return array
  	 */
  	private function readISO6709(string $val_ISO6709): array
  	{
  		$return = [
  			'latitude' => null,
  			'longitude' => null,
  			'altitude' => null,
  		];
  		$matches = [];
  		// Adjustment compared to https://github.com/seanson/python-iso6709.git
  		// Altitude have format +XX.XXXX -> Adjustment for decimal
  		preg_match('/^(?<lat_sign>\+|-)(?<lat_degrees>[0,1]?\d{2})(?<lat_minutes>\d{2}?)?(?<lat_seconds>\d{2}?)?(?<lat_fraction>\.\d+)?(?<lng_sign>\+|-)(?<lng_degrees>[0,1]?\d{2})(?<lng_minutes>\d{2}?)?(?<lng_seconds>\d{2}?)?(?<lng_fraction>\.\d+)?(?<alt>[\+\-][0-9]\d*(\.\d+)?)?\/$/', $val_ISO6709, $matches);
  		$return['latitude'] = $this->convertDMStoDecimal($matches['lat_sign'], $matches['lat_degrees'], $matches['lat_minutes'], $matches['lat_seconds'], $matches['lat_fraction']);
  		$return['longitude'] = $this->convertDMStoDecimal($matches['lng_sign'], $matches['lng_degrees'], $matches['lng_minutes'], $matches['lng_seconds'], $matches['lng_fraction']);
  		if (isset($matches['alt'])) {
  			$return['altitude'] = doubleval($matches['alt']);
  		}
  		return $return;
  	}

    /**
     * Returns an array of IPTC data
     *
     * @param string $file The file to read the IPTC data from
     * @return array
     */
    public function getIptcData($file)
    {
        getimagesize($file, $info);
        $arrData = array();
        if (isset($info['APP13'])) {
            $iptc = iptcparse($info['APP13']);

            foreach ($this->iptcMapping as $name => $field) {
                if (!isset($iptc[$field])) {
                    continue;
                }

                if (count($iptc[$field]) === 1) {
                    $arrData[$name] = reset($iptc[$field]);
                } else {
                    $arrData[$name] = $iptc[$field];
                }
            }
        }

        return $arrData;
    }
}
