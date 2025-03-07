import { Card, CardContent, CardHeader, CardTitle } from "@/Pages/Leauge/components/ui/card"
import { useTeamContext } from "./context/TeamContext"
import { Progress } from "@/Pages/Leauge/components/ui/progress"
import { Button } from "@/Pages/Leauge/components/ui/button"
import { useState } from "react"

export default function Predictions() {
  const { teams, currentWeek, isSimulationComplete, updatePredictions } = useTeamContext();
  const [loading, setLoading] = useState(false);

  const sortedTeams = [...teams].sort((a, b) => b.win_probability - a.win_probability);

  const hasPredictions = teams.some(team => team.win_probability > 0);

  const handleUpdatePredictions = async () => {
    setLoading(true);
    await updatePredictions();
    setLoading(false);
  }

  return (
    <Card>
      <CardHeader className="bg-green-700 text-white dark:bg-gray-700 flex flex-row justify-between items-center">
        <CardTitle>Championship Predictions</CardTitle>
        {currentWeek >= 4 && !isSimulationComplete && (
          <Button
            variant="outline"
            size="sm"
            onClick={handleUpdatePredictions}
            disabled={loading}
            className="bg-white hover:bg-gray-100 text-green-700 border-white"
          >
            {loading ? "Updating..." : "Update Predictions"}
          </Button>
        )}
      </CardHeader>
      <CardContent className="p-6 pt-4">
        {currentWeek < 4 ? (
          <div className="text-center py-8">
            <p className="text-lg text-gray-500 dark:text-gray-400">Predictions will be available after week
              3</p>
          </div>
        ) : !hasPredictions && !isSimulationComplete ? (
          <div className="text-center py-8">
            <div className="mb-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-12 w-12 mx-auto text-gray-400 mb-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={1.5}
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              <p className="text-lg text-gray-500 dark:text-gray-400 mb-4">No predictions available
                yet</p>
              <Button
                onClick={handleUpdatePredictions}
                disabled={loading}
                className="mx-auto"
              >
                {loading ? "Calculating..." : "Calculate Predictions"}
              </Button>
            </div>
          </div>
        ) : isSimulationComplete ? (
          <div className="space-y-6">
            <h3 className="text-xl font-bold text-center">{sortedTeams[0].name} is the Champion!</h3>
            <div className="grid gap-4">
              {sortedTeams.map((team) => (
                <div key={team.id} className="space-y-2">
                  <div className="flex justify-between">
                    <div className="flex items-center gap-2">
                      <div
                        className="w-6 h-6 rounded-full bg-green-700 flex items-center justify-center text-white dark:bg-gray-600">
                        {team.name.charAt(0)}
                      </div>
                      <span className="font-medium">{team.name}</span>
                    </div>
                    <span
                      className="font-bold">{team.id === sortedTeams[0].id ? "100%" : "0%"}</span>
                  </div>
                  <Progress
                    value={team.id === sortedTeams[0].id ? 100 : 0}
                    className={team.id === sortedTeams[0].id ? "bg-green-100" : ""}
                  />
                </div>
              ))}
            </div>
          </div>
        ) : (
          <div className="space-y-6">
            <h3 className="text-lg font-medium">Championship Probability</h3>
            <div className="grid gap-4">
              {sortedTeams.map((team) => (
                <div key={team.id} className="space-y-2">
                  <div className="flex justify-between">
                    <div className="flex items-center gap-2">
                      <div
                        className="w-6 h-6 rounded-full bg-green-700 flex items-center justify-center text-white dark:bg-gray-600">
                        {team.name.charAt(0)}
                      </div>
                      <span className="font-medium">{team.name}</span>
                    </div>
                    <span className="font-bold">{team.win_probability}%</span>
                  </div>
                  <Progress value={team.win_probability} className="h-2" />
                </div>
              ))}
            </div>

            <div
              className="bg-green-50 p-4 rounded-lg border border-green-200 mt-8 dark:bg-gray-800 dark:border-gray-700">
              <h4 className="font-medium mb-2">How predictions work</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                Predictions are calculated based on current points, remaining fixtures, team strength,
                and historical
                performance. The algorithm considers head-to-head matches, goal difference, and other
                factors to
                estimate each team's chance of winning the league.
              </p>
            </div>
          </div>
        )}
      </CardContent>
    </Card>
  )
}

