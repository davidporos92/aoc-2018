package main

import (
	"fmt"
	"time"
)

var input = 8868

const powerCellGridSize = 300

var powerCells [powerCellGridSize][powerCellGridSize]int

func main() {
	start := time.Now()
	maxPowerLevelGrid := make(map[string]int)
	maxPowerLevelGrid["x"] = 0
	maxPowerLevelGrid["y"] = 0
	maxPowerLevelGrid["p"] = 0

	for x := 0; x < powerCellGridSize; x++ {
		for y := 0; y < powerCellGridSize; y++ {
			powerCells[x][y] = getPowerLevel(x, y)

			if x >= 2 && y >= 2 {
				xs, ys := getCoordinatesForSize(x, y, 3, true)
				gridPowerLevel := getPowerLevelForCoordinates(xs, ys)

				if maxPowerLevelGrid["p"] < gridPowerLevel {
					maxPowerLevelGrid["x"] = x - 2
					maxPowerLevelGrid["y"] = y - 2
					maxPowerLevelGrid["p"] = gridPowerLevel
				}
			}
		}
	}

	fmt.Printf("Part 1: %d,%d\n", maxPowerLevelGrid["x"], maxPowerLevelGrid["y"])

	maxPowerLevelGrid["x"] = 0
	maxPowerLevelGrid["y"] = 0
	maxPowerLevelGrid["s"] = 0
	maxPowerLevelGrid["p"] = 0

	for x := 0; x < powerCellGridSize; x++ {
		for y := 0; y < powerCellGridSize; y++ {
			fmt.Printf("%d,%d\n", x, y)
			for size := 1; size < min(powerCellGridSize-x, powerCellGridSize-y); size++ {
				xs, ys := getCoordinatesForSize(x, y, size, false)
				gridPowerLevel := getPowerLevelForCoordinates(xs, ys)

				if maxPowerLevelGrid["p"] < gridPowerLevel {
					maxPowerLevelGrid["x"] = x
					maxPowerLevelGrid["y"] = y
					maxPowerLevelGrid["s"] = size
					maxPowerLevelGrid["p"] = gridPowerLevel
				}
			}
		}
	}

	fmt.Printf("Part 2: %d,%d,%d\n", maxPowerLevelGrid["x"], maxPowerLevelGrid["y"], maxPowerLevelGrid["s"])
	elapsed := time.Since(start)
	fmt.Printf("Execution took %s", elapsed)
}

func getPowerLevel(x, y int) (power int) {
	rackId := x + 10
	power = ((((rackId * y) + input) * rackId) / 100 % 10) - 5
	return
}

func getCoordinatesForSize(x, y, size int, backwards bool) (xs, ys []int) {
	for i := 0; i < size; i++ {
		if backwards {
			xs = append(xs, x-i)
			ys = append(ys, y-i)
			continue
		}

		xs = append(xs, x+i)
		ys = append(ys, y+i)
	}

	return
}

func getPowerLevelForCoordinates(xs, ys []int) (p int) {
	for _, x := range xs {
		for _, y := range ys {
			p += powerCells[x][y]
		}
	}

	return
}

func min(a, b int) int {
	if a < b {
		return a
	}

	return b
}
