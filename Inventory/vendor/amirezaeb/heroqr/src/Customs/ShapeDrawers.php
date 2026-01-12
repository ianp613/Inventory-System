<?php

namespace HeroQR\Customs;

/**
 * Class ShapeDrawers
 *
 * This class provides static methods to draw various shapes (star, square, circle, diamond, heart, askew square, plus, small circle, twinkilng star)
 * on a GD image resource. Each method is designed to handle specific shape-drawing logic
 * based on the provided parameters such as position, size, and color.
 *
 * @package HeroQR\Customs
 */
class ShapeDrawers
{
    /**
     * Draws a star shape on the image
     */
    public static function drawStar(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $points = [];
        for ($i = 0; $i < 10; $i++) {
            $angle = $i * M_PI / 5 + M_PI / 2;
            $radius = ($i % 2 === 0) ? $baseBlockSize / 2 : $baseBlockSize / 4;
            $points[] = intval($columnIndex * $baseBlockSize + $baseBlockSize / 2 + $radius * cos($angle));
            $points[] = intval($rowIndex * $baseBlockSize + $baseBlockSize / 2 - $radius * sin($angle));
        }
        imagefilledpolygon($baseImage, $points, $foregroundColor);
    }


    /**
     * Draws a square shape on the image
     */
    public static function drawSquare(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        imagefilledrectangle(
            $baseImage,
            $columnIndex * $baseBlockSize,
            $rowIndex * $baseBlockSize,
            ($columnIndex + 1) * $baseBlockSize - 1,
            ($rowIndex + 1) * $baseBlockSize - 1,
            $foregroundColor
        );
    }

    /**
     * Draws a circle shape on the image
     */
    public static function drawCircle(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        imagefilledellipse(
            $baseImage,
            intval($columnIndex * $baseBlockSize + $baseBlockSize / 2),
            intval($rowIndex * $baseBlockSize + $baseBlockSize / 2),
            intval($baseBlockSize * 0.8),
            intval($baseBlockSize * 0.8),
            $foregroundColor
        );
    }

    /**
     * Draws a diamond shape on the image
     */
    public static function drawDiamond(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;
        $halfSize = $baseBlockSize / 2.2;

        imagefilledpolygon($baseImage, [
            $centerX,
            $centerY - $halfSize,
            $centerX + $halfSize,
            $centerY,
            $centerX,
            $centerY + $halfSize,
            $centerX - $halfSize,
            $centerY,
        ], $foregroundColor);
    }

    /**
     * Draws a heart shape on the image
     */
    public static function drawHeart(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $points = [];

        // Fewer points for a compact shape
        $n = 50;
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;
        $scale   = $baseBlockSize / 32; // Smaller scale than before

        for ($i = 0; $i < $n; $i++) {
            $t = M_PI * 2 * $i / $n;
            $x = 16 * pow(sin($t), 3);
            $y = 13 * cos($t) - 5 * cos(2 * $t) - 2 * cos(3 * $t) - cos(4 * $t);
            $points[] = intval($centerX + $x * $scale);
            $points[] = intval($centerY - $y * $scale);
        }

        imagefilledpolygon($baseImage, $points, $foregroundColor);
    }

    /**
     * Draws a random askew square (quadrilateral) on the image
     */
    public static function drawAskewSquare(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        // Random rotation angle between -10 and +10 degrees
        $angleDegrees = rand(-10, 10);
        $angle = deg2rad($angleDegrees);

        // Slightly smaller square (e.g. 85% of block size)
        $scale = 0.85;
        $halfSize = ($baseBlockSize * $scale) / 2;

        // Center of the square
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;

        // Define corners before rotation
        $corners = [
            [-$halfSize, -$halfSize], // Top-left
            [$halfSize, -$halfSize],  // Top-right
            [$halfSize, $halfSize],   // Bottom-right
            [-$halfSize, $halfSize],  // Bottom-left
        ];

        $rotatedPoints = [];

        foreach ($corners as [$x, $y]) {
            // Apply rotation
            $rotatedX = $x * cos($angle) - $y * sin($angle);
            $rotatedY = $x * sin($angle) + $y * cos($angle);

            // Translate to center
            $rotatedPoints[] = intval($centerX + $rotatedX);
            $rotatedPoints[] = intval($centerY + $rotatedY);
        }

        imagefilledpolygon($baseImage, $rotatedPoints, 4, $foregroundColor);
    }

    /**
     * Draws a plus pattern centered in the block
     */
    public static function drawPlus(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;

        $armThickness = intval($baseBlockSize * 0.3); // Bolder arms
        $armLength = intval($baseBlockSize * 0.8);     // Longer arms

        // Horizontal arm
        imagefilledrectangle(
            $baseImage,
            intval($centerX - $armLength / 2),
            intval($centerY - $armThickness / 2),
            intval($centerX + $armLength / 2),
            intval($centerY + $armThickness / 2),
            $foregroundColor
        );

        // Vertical arm
        imagefilledrectangle(
            $baseImage,
            intval($centerX - $armThickness / 2),
            intval($centerY - $armLength / 2),
            intval($centerX + $armThickness / 2),
            intval($centerY + $armLength / 2),
            $foregroundColor
        );
    }

    /**
     * Draws a slightly smaller circle shape on the image
     */
    public static function drawSmallCircle(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        $diameter = intval($baseBlockSize * 0.5);

        imagefilledellipse(
            $baseImage,
            intval($columnIndex * $baseBlockSize + $baseBlockSize / 2),
            intval($rowIndex * $baseBlockSize + $baseBlockSize / 2),
            $diameter,
            $diameter,
            $foregroundColor
        );
    }

    /**
     * Draws a twinkling star with straight-pointing arms
     */
    public static function drawTwinklingStar(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;
        $radius = $baseBlockSize * 0.4;

        $points = [];

        // Create 8-point star with straight-facing arms
        for ($i = 0; $i < 8; $i++) {
            // Use multiples of 45° starting from 0°
            $angle = deg2rad($i * 45);
            $length = ($i % 2 === 0) ? $radius : $radius * 0.5;

            $x = intval($centerX + cos($angle) * $length);
            $y = intval($centerY + sin($angle) * $length);
            $points[] = $x;
            $points[] = $y;
        }

        imagefilledpolygon($baseImage, $points, count($points) / 2, $foregroundColor);
    }
    
}