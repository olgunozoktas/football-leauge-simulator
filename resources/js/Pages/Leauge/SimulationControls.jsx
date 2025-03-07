import {Card, CardContent, CardHeader, CardTitle} from "@/Pages/Leauge/components/ui/card"
import {Button} from "@/Pages/Leauge/components/ui/button"
import {useTeamContext} from "./context/TeamContext"
import {Play, FastForward, RotateCcw} from "lucide-react"

export default function SimulationControls() {
    const {simulateNextWeek, simulateAllWeeks, resetSimulation, currentWeek, isSimulationComplete} = useTeamContext();

    return (
        <Card>
            <CardHeader className="bg-green-700 text-white dark:bg-gray-700">
                <CardTitle>Simulation Controls</CardTitle>
            </CardHeader>
            <CardContent className="p-6 pt-4">
                <div className="space-y-4">
                    <Button className="w-full" onClick={simulateNextWeek} disabled={isSimulationComplete}>
                        <Play className="mr-2 h-4 w-4"/>
                        Play Next Week
                    </Button>

                    <Button className="w-full" variant="secondary" onClick={simulateAllWeeks}
                            disabled={isSimulationComplete}>
                        <FastForward className="mr-2 h-4 w-4"/>
                        Simulate All Weeks
                    </Button>

                    <Button className="w-full" variant="outline" onClick={resetSimulation}>
                        <RotateCcw className="mr-2 h-4 w-4"/>
                        Reset Simulation
                    </Button>

                    <div
                        className="bg-green-50 p-4 rounded-lg border border-green-200 mt-4 dark:bg-gray-800 dark:border-gray-700">
                        <h4 className="font-medium mb-2">Simulation Info</h4>
                        <p className="text-sm text-gray-600 dark:text-gray-300">
                            {isSimulationComplete
                                ? "Simulation complete! You can reset to start over."
                                : currentWeek === 0
                                    ? "Click 'Play Next Week' to start the simulation."
                                    : `Currently at Week ${currentWeek} of 6. ${6 - currentWeek} weeks remaining.`}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    )
}

