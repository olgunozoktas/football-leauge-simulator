import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Pages/Leauge/components/ui/tabs"
import { Badge } from "@/Pages/Leauge/components/ui/badge"
import { Button } from "@/Pages/Leauge/components/ui/button"
import EditMatchModal from "./EditMatchModal"

export default function FixturesTabs({
    fixtures,
    currentWeek,
    hasStartedWeeks,
    simulateMatch,
    handleEditMatch,
    handleSaveResult
}) {
    const fixturesByWeek = fixtures.reduce((acc, fixture) => {
        if (!acc[fixture.week]) {
            acc[fixture.week] = [];
        }
        acc[fixture.week].push(fixture);
        return acc;
    }, {});

    const totalWeeks = fixtures.length > 0
        ? Math.max(...fixtures.map(fixture => fixture.week))
        : 6;

    return (
        <Tabs defaultValue={currentWeek > 0 ? currentWeek.toString() : "1"} className="w-full mt-4">
            <TabsList className="grid grid-cols-6 mb-4">
                {Array.from({ length: totalWeeks }, (_, i) => i + 1).map((week) => (
                    <TabsTrigger
                        key={week}
                        value={week.toString()}
                        disabled={!hasStartedWeeks}
                        className={!hasStartedWeeks ? "cursor-not-allowed opacity-60" : ""}
                    >
                        Week {week}
                    </TabsTrigger>
                ))}
            </TabsList>

            {Array.from({ length: totalWeeks }, (_, i) => i + 1).map((week) => (
                <TabsContent key={week} value={week.toString()} className="space-y-4">
                    {!hasStartedWeeks ? (
                        <div className="p-4 rounded-lg border bg-gray-50 text-center dark:bg-gray-800">
                            <p className="text-gray-500 dark:text-gray-400">
                                The league hasn't started yet. Start the first week to view fixtures.
                            </p>
                        </div>
                    ) : fixturesByWeek[week]?.length > 0 ? (
                        fixturesByWeek[week].map((match) => (
                            <div
                                key={match.id}
                                className={`p-4 rounded-lg border ${match.played ? "bg-green-50 dark:bg-gray-700" : "bg-white dark:bg-gray-800"
                                    }`}
                            >
                                <div className="flex justify-between items-center">
                                    <div className="flex items-center gap-3 flex-1">
                                        <div
                                            className="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center text-white dark:bg-gray-600 font-bold">
                                            {match.home_team?.name?.charAt(0)}
                                        </div>
                                        <div>
                                            <span
                                                className="font-medium text-base">{match.home_team?.name}</span>
                                            <div className="flex items-center mt-1">
                                                <div
                                                    className="text-xs text-gray-600 dark:text-gray-400 mr-2">
                                                    Power: {match.home_team?.power}
                                                </div>
                                                <div
                                                    className="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div
                                                        className="h-full bg-green-600"
                                                        style={{ width: `${(match.home_team?.power / 100) * 100}%` }}
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex flex-col items-center mx-2">
                                        {match.played ? (
                                            <>
                                                <div className="flex items-center gap-2 mb-1">
                                                    <span
                                                        className="text-xl font-bold">{match.home_goals}</span>
                                                    <span className="text-sm">-</span>
                                                    <span
                                                        className="text-xl font-bold">{match.away_goals}</span>
                                                </div>
                                                <Badge className="bg-green-600">Played</Badge>
                                            </>
                                        ) : (
                                            <>
                                                <div
                                                    className="text-sm text-gray-500 mb-1">Week {match.week}</div>
                                                <Badge variant="outline">Not Played</Badge>
                                            </>
                                        )}
                                    </div>

                                    <div className="flex items-center gap-3 flex-1 justify-end">
                                        <div className="text-right">
                                            <span
                                                className="font-medium text-base">{match.away_team?.name}</span>
                                            <div className="flex items-center justify-end mt-1">
                                                <div
                                                    className="w-16 h-2 bg-gray-200 rounded-full overflow-hidden mr-2">
                                                    <div
                                                        className="h-full bg-green-600"
                                                        style={{ width: `${(match.away_team?.power / 100) * 100}%` }}
                                                    ></div>
                                                </div>
                                                <div className="text-xs text-gray-600 dark:text-gray-400">
                                                    Power: {match.away_team?.power}
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            className="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center text-white dark:bg-gray-600 font-bold">
                                            {match.away_team?.name?.charAt(0)}
                                        </div>
                                    </div>
                                </div>

                                <div className="flex justify-center mt-4 gap-2">
                                    {!match.played && week === currentWeek && (
                                        <Button size="sm" onClick={() => simulateMatch(match.id)}>
                                            Play Match
                                        </Button>
                                    )}

                                    {match.played && (
                                        <EditMatchModal
                                            match={match}
                                            onSave={handleSaveResult}
                                            trigger={
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    onClick={() => handleEditMatch(match)}
                                                >
                                                    Edit Result
                                                </Button>
                                            }
                                        />
                                    )}
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="p-4 rounded-lg border bg-gray-50 text-center dark:bg-gray-800">
                            <p className="text-gray-500 dark:text-gray-400">No fixtures scheduled for
                                Week {week}.</p>
                        </div>
                    )}
                </TabsContent>
            ))}
        </Tabs>
    );
} 