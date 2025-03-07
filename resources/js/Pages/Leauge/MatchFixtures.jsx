import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/Pages/Leauge/components/ui/card"
import { useTeamContext } from "./context/TeamContext"
import { Alert, AlertDescription, AlertTitle } from "./components/ui/alert"
import { AlertCircle } from "lucide-react"
import { Button } from "@/Pages/Leauge/components/ui/button"
import axios from "axios"
import FixturesTabs from "./components/FixturesTabs"

export default function MatchFixtures() {
    const { fixtures, currentWeek, simulateMatch, updateMatchResult, fetchInitialData } = useTeamContext();
    const [editMatch, setEditMatch] = useState(null);
    const [loading, setLoading] = useState(false);

    const handleEditMatch = (match) => {
        setEditMatch(match)
    }

    const handleSaveResult = async (matchId, homeGoals, awayGoals) => {
        try {
            await updateMatchResult(matchId, homeGoals, awayGoals);
            setEditMatch(null);
            return Promise.resolve(); // Explicitly return a resolved promise
        } catch (error) {
            console.error("Error updating match result:", error);
            return Promise.reject(error); // Return a rejected promise if there's an error
        }
    }

    const generateFixtures = async () => {
        try {
            setLoading(true)
            await axios.post('/api/fixtures/generate')
            await fetchInitialData()
            setLoading(false)
        } catch (error) {
            console.error('Error generating fixtures:', error)
            setLoading(false)
        }
    }

    const hasFixtures = fixtures.length > 0;
    const hasStartedWeeks = currentWeek > 0;

    return (
        <Card>
            <CardHeader className="bg-green-700 text-white dark:bg-gray-700">
                <CardTitle>Match Fixtures</CardTitle>
            </CardHeader>
            <CardContent className="p-4 pt-4">
                {!hasFixtures ? (
                    <div className="text-center py-12 px-4">
                        <div className="mb-6">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-16 w-16 mx-auto text-green-600 mb-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={1.5}
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                />
                            </svg>
                            <h3 className="text-xl font-semibold mb-2">No Fixtures Available</h3>
                            <p className="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
                                Generate fixtures to create a schedule of matches between all teams in the league.
                            </p>
                        </div>
                        <Button
                            onClick={generateFixtures}
                            disabled={loading}
                            className="mx-auto px-6 py-2 bg-green-600 hover:bg-green-700"
                            size="lg"
                        >
                            {loading ? (
                                <>
                                    <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            strokeWidth="4"></circle>
                                        <path className="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generating Fixtures...
                                </>
                            ) : (
                                <>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="h-5 w-5 mr-2"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                    Generate Fixtures
                                </>
                            )}
                        </Button>
                    </div>
                ) : (
                    <>
                        {!hasStartedWeeks && (
                            <Alert variant="warning" className="mb-4 bg-amber-50 border-amber-200 text-amber-800">
                                <AlertCircle className="h-4 w-4" />
                                <AlertTitle>No Weeks Started</AlertTitle>
                                <AlertDescription>
                                    The league hasn't started yet. Start the first week to begin playing matches.
                                </AlertDescription>
                            </Alert>
                        )}

                        <FixturesTabs
                            fixtures={fixtures}
                            currentWeek={currentWeek}
                            hasStartedWeeks={hasStartedWeeks}
                            simulateMatch={simulateMatch}
                            handleEditMatch={handleEditMatch}
                            handleSaveResult={handleSaveResult}
                        />
                    </>
                )}
            </CardContent>
        </Card>
    )
}

