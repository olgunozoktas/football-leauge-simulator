import {Card, CardContent, CardHeader, CardTitle} from "@/Pages/Leauge/components/ui/card"
import {Accordion, AccordionContent, AccordionItem, AccordionTrigger} from "@/Pages/Leauge/components/ui/accordion"

export default function FAQ() {
    return (
        <Card>
            <CardHeader className="bg-green-700 text-white dark:bg-gray-700">
                <CardTitle>Frequently Asked Questions</CardTitle>
            </CardHeader>
            <CardContent className="p-6">
                <Accordion type="single" collapsible className="w-full">
                    <AccordionItem value="item-1">
                        <AccordionTrigger>What are Football Rules?</AccordionTrigger>
                        <AccordionContent>
                            Three points for a win is a standard used in many sports leagues and group tournaments,
                            especially in
                            association football, in which three (rather than two) points are awarded to the team
                            winning a match,
                            with no points awarded to the losing team. If the game is drawn, each team receives one
                            point. The system
                            places additional value on wins compared to draws such that teams with a higher number of
                            wins may rank
                            higher in tables than teams with a lower number of wins but more draws.
                        </AccordionContent>
                    </AccordionItem>

                    <AccordionItem value="item-2">
                        <AccordionTrigger>What is a league?</AccordionTrigger>
                        <AccordionContent>
                            The tournament proper begins with a group stage of 32 teams, divided into eight groups of
                            four. Seeding is
                            used whilst making the draw for this stage, whilst teams from the same nation may not be
                            drawn into groups
                            together. Each team plays six group stage games, meeting the other three teams in its group
                            home and away
                            in a round-robin format.
                        </AccordionContent>
                    </AccordionItem>

                    <AccordionItem value="item-3">
                        <AccordionTrigger>What is a champions league?</AccordionTrigger>
                        <AccordionContent>
                            The UEFA Champions League (abbreviated as UCL) is an annual club football competition
                            organised by the
                            Union of European Football Associations (UEFA) and contested by top-division European clubs,
                            deciding the
                            competition winners through a round robin group stage to qualify for a double-legged
                            knockout format, and
                            a single leg final. It is one of the most prestigious football tournaments in the world and
                            the most
                            prestigious club competition in European football, played by the national league champions
                            (and, for some
                            nations, one or more runners-up) of their national associations.
                        </AccordionContent>
                    </AccordionItem>

                    <AccordionItem value="item-4">
                        <AccordionTrigger>What is a fixture?</AccordionTrigger>
                        <AccordionContent>
                            The teams will be split into four seeding pots. Pot 1 will consist of the holders, the UEFA
                            Europa League
                            winners and the champions of the six highest-ranked nations who did not qualify via one of
                            the 2020/21
                            continental titles; Pots 2 to 4 will be determined by the club coefficient rankings. No team
                            can play a
                            side from their own association. Any other restrictions will be announced ahead of the draw
                            ceremony. In
                            the case of associations with two representatives, clubs will be paired in order to split
                            their matches
                            between Tuesdays and Wednesdays. In the case of associations with four (or more)
                            representatives, two
                            pairings will be made.
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </CardContent>
        </Card>
    )
}

